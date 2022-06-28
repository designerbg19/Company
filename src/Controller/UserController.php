<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Mail;
use App\Entity\User;
use App\Event\EmailEvent;
use App\Form\UserType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="user_api")
 */
class UserController extends MainController
{
    /**
     * @Route("/users", name="get_all_users", methods={"GET"})
     */
    public function getAllUsers()
    {
        $users = $this->em->getRepository(User::class)->findAll();
        $data = $this->getData($users);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/users/{id}", name="get_one_user", methods={"GET"})
     */
    public function getById($id)
    {
        $user = $this->em->getRepository(User::class)->findBy(['id'=>$id]);
        $data = $this->getData($user);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/sendEmailAdmin/{email}", name="send_mail_admin", methods={"POST"})
     */
    public function sendEmailAdmin($email, Request $request, EventDispatcherInterface $dispatcher)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if(is_null($user))
        {
            return $this->successResponse(["code" => 409, "message" => 'User not exists'],409);
        }
        $data = $this->jsonDecode($request);
        $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'administrateur_nouveau_contact'));
        $message = array('subject' => $data['subject'],'message' => $data['message']);
        $dispatcher->dispatch(new EmailEvent($mail,$user,$message),EmailEvent::ADMIBUSER);
        return $this->successResponse($data);

    }

    /**
     * @Route("/users/{id}", name="update_users", methods={"PATCH","POST"})
     */
    public function update($id, Request $request, FileUploader $fileUploader,PersistService $persistService,EventDispatcherInterface $dispatcher)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $data = $this->jsonDecode($request);
        $persistService->update($request,UserType::class,$user,$data);
        $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'votre_compte_professionnel'));
        $file = $fileUploader->upload($request);
        if($file != null){
            $result = $fileUploader->ImageUploade($file, $this->em);
            $user->setImages($result);
        }
        if(isset($data['company'])) {
            $company = $this->em->getRepository(Company::class)->find($data['company']);
            if ($company === null) {
                return $this->successResponse(["code" => 409, "message" => 'company not exist']);
            }
            $user->setCompany($company);
        }
        if(isset($data['professionalProfile'])) {
            $user->setProfessionalProfile(true);
            $company = $this->em->getRepository(Company::class)->findOneBy(['user' => $id]);
            if($company == null && isset($data['company']) == false){
                return $this->successResponse(["code" => 409, "message" => 'company not exist']);
            } elseif ($company == null) {
                $company = $this->em->getRepository(Company::class)->find($data['company']);
            }
            $dispatcher->dispatch(new EmailEvent($mail, $user, $company), EmailEvent::ACCOUNTPRO);
        }
        if(isset($data['birthDate'])) {
            $date = new \DateTime($data['birthDate']);
            $user->setBirthDate($date);
        }
        $this->em->persist($user);
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'user successfully edit']);
    }

    /**
     * @Route("/professionalAccountEnabled/{id}", name="enabled_professionalAccount", methods={"POST"})
     */
    public function enabledAccount(EventDispatcherInterface $dispatcher,$id)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $company = $this->em->getRepository(Company::class)->findOneBy(['user' => $id]);
        if($company == null) {
            return $this->successResponse(["code" => 409, "message" => "company not exist"],409);
        }
        $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'votre_compte_professionnel_active'));
        try {
            $user->setEnabled(true);
            $this->em->persist($user);
            $this->em->flush();
            $dispatcher->dispatch(new EmailEvent($mail,$user,$company),EmailEvent::ACCOUNTPRO);
            return $this->successResponse(["code" => 200, "message" => "Professional account is activated"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/removeProfessionalAccount/{id}", name="delete_professionalAccount", methods={"POST"})
     */
    public function deleteProfessionalAccount(int $id)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        try {
            $user->setEnabled(false);
            $user->setProfessionalProfile(false);
            $this->em->persist($user);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "Professional account is deleted"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     *  @Route("/updatePassword/{id}", name="edit_passwrd_user", methods={"POST"})
     */
    public function changeUserPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder,$id,PersistService $persistService) {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id]);
        $data = $this->jsonDecode($request);
        $persistService->update($request,UserType::class,$user,$data);
        $checkPass = $passwordEncoder->isPasswordValid($user, $data['old_password']);
        if($checkPass === true) {
            $user->setPassword($passwordEncoder->encodePassword($user, $data['new_password']));
            $this->em->persist($user);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'password successfully edit']);
        } else {
            return $this->successResponse(['message' => 'The current password is incorrect.']);
        }
    }

    /**
     * @Route("/users", name="delete_user", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $user = $this->em->getRepository(User::class)->find($id);
            $this->em->remove($user);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'user successfully delete']);
    }

    /**
     * @param array $users
     * @return array
     */
    public function getData(array $users): array
    {
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUserName(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'civility' => $user->getCivility(),
                'tva' => $user->getTva(),
                'site' => $user->getSite(),
                'country' => $user->getCountry(),
                'birthDate' => $user->getBirthDate(),
                'siren' => $user->getSiren(),
                'status' => $user->getStatus(),
                'roles' => $user->getRoles(),
                'enabled' => $user->isEnabled(),
                'professionalProfile' => $user->isProfessionalProfile(),
                'nationality' => $user->getNationality(),
                'responsable' => $user->getResponsable(),
                'lastLogin' => $user->getLastLogin(),
                'emailPro' => $user->getEmailPro(),
                'webSite' => $user->getWebSite(),
                'phone' => $user->getPhone(),
                'codePostal' => $user->getCodePostal(),
                'mediaManager' => $user->getMediaManager(),
                'image' => $user->getImages(),
                'badge' => $user->getBadge()
            ];

        }
        foreach ($data as $key => $d) {
            $company = $this->em->getRepository(Company::class)->findOneBy(['user' => $d['id']]);
            $leisures = $this->em->getRepository(User::class)->findLeisureByUser($d['id']);
            if (isset($company)) {
                $data[$key]['company'][] = [
                    'id' => $company->getId(),
                    'name' => $company->getName(),
                ];
            }
            if (isset($leisures)) {
                foreach ($leisures as $leisure) {
                    $data[$key]['leisure'][] = [
                        'id' => $leisure['id'],
                        'name' => $leisure['name'],
                    ];
                }
            }
        }
        return $data;
    }

}