<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Mail;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Form\ResetPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\PersistService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AuthController extends MainController
{
    /**
     * @Route("/register", name="user_registration", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,PersistService $persistService, EventDispatcherInterface $dispatcher)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->em->getRepository(User::class)->findOneBy([
            "email" => $data['email']
        ]);
        $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'Bienvenue_RateACompany'));

        if(!is_null($user))
        {
            return $this->successResponse(["code" => 409, "message" => 'User already exists'],409);
        }
        $user = new User();
        if (empty($data['username']) || empty($data['password']) || empty($data['email'])){
            return $this->successResponse(["code" => 409, "message" => 'Invalid Username or Password or Email'],409);
        }
        $persistService->insert($request,UserType::class,$user,$data);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $data['plainPassword']
            )
        );
        $dispatcher->dispatch(new EmailEvent($mail,$user,$data['username']),EmailEvent::USER);
        $this->em->persist($user);
        $this->em->flush();
        return $this->successResponse($data);
    }

    /**
     * @Route("/login/social", name="google_registration", methods={"POST"})
     */
    public function loginGoogle(Request $request,JWTTokenManagerInterface $JWTManager,UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->em->getRepository(User::class)->findOneBy([
            "email" => $data['email']
        ]);
        if (!$user) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setLastname($data['lastName']);
            $user->setFirstname($data['firstName']);
            $user->setUsername($data['name']);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    'secret123'
                )
            );
            $user->setCivility('M');
            $this->em->persist($user);
            $this->em->flush();
        }
        return $this->successResponse(['token' => $JWTManager->create($user)]);
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     * @Route("/login", name="user_login")
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    /**
     * @Route("/reset/reset-password", name="app_forgotten_password",methods={"POST"})
     */
    public function resetPassword(Request $request, UserRepository $users, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $data = $this->jsonDecode($request);
        // On initialise le formulaire
        $form = $this->createForm(ResetPasswordType::class);
        // On traite le formulaire
        $form->handleRequest($request);
        $form->submit($data);
        // On cherche un utilisateur ayant cet e-mail
        $user = $users->findOneByEmail($data['email']);
        // Si l'utilisateur n'existe pas
        if ($user === null) {
            return $this->successResponse(["code" => 409, "message" => 'Invalid Email']);
        } else {
            $token = $this->randomPassword(20);
            try {
                $user->setResetToken($token);
                $this->em->persist($user);
                $this->em->flush();
            } catch (\Exception $e) {
                return $this->successResponse(["code" => 409, "message" => 'Invalid token']);
            }
            // On génère l'URL de réinitialisation de mot de passe
            $urlRoute = $this->generateUrl('app_reset_password', ['token' => $token]);
            $url = $this->getParameter('app_front_hostname') . $urlRoute;
            $messages = (new Email())
                ->from('contact@rateacompany.com')
                ->to($user->getEmail())
                ->subject('Mot de passe oublié')
                ->html("Bonjour,<br><br>Une demande de réinitialisation de mot de passe a été effectuée pour le site 
rateACompany.com. Veuillez cliquer sur le lien suivant : " . $url, 'text/html');
            $mailer->send($messages);
            return $this->successResponse(["code" => 200, "message" => 'mail successfully send']);
        }
    }

    /**
     * @Route("/reset-password", name="app_reset_password")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $token = $request->get('token');
        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
        $data = $this->jsonDecode($request);
        // Si l'utilisateur n'existe pas
        if ($user === null) {
            return $this->successResponse(["code" => 409, "message" => 'Invalid token']);
        }
        if (null !== $data) {
            // On supprime le token
            $user->setResetToken(null);
            // On chiffre le mot de passe
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $data['plainPassword']
                )
            );
            $this->em->persist($user);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'password successfully edit']);
        }
    }

    /**
     * @Route("/companies", name="get_companies_name", methods={"GET"})
     */
    public function getNameCompanies()
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        $data = [];
        foreach ($companies as $value) {
            $data[] = [
                'id' => $value->getId(),
                'name' => $value->getName(),
            ];
        }
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }
}