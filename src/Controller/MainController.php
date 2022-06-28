<?php

namespace App\Controller;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Entity\Notifications;
use App\Form\NotificationsType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    public $em;
    public $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $object
     * @return Response
     */
    public function successResponse($object, $staus = null)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($object, 'json');
        if ($staus) {
            $response = new Response($data, $staus);
        } else {
            $response = new Response($data);
        }
        $response->headers->set('Content-type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }


    /**
     * Function for rundom password for the merchs (generate password)
     * @param int $numberOfChars
     * @return string
     */
    public function randomPassword(int $numberOfChars)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $numberOfChars; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function jsonDecode($request)
    {
        return json_decode($request->getContent(), true);
    }

    public function notification($user,$note,$suggestion,Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $notification = new Notifications();
        $persistService->insert($request,NotificationsType::class,$notification,$data);
        $notification->setUser($user);
        $notification->setDate(new \DateTime());
        if (isset($note)) {
            $notification->setMessage($user->getUsername() . " a répondu à votre note pour l'entreprise " . $note->getNote()->getCompany()->getName());
            $url = $this->generateUrl('get_one_company', array('id' => $note->getNote()->getCompany()->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $notification->setMessage($user->getUsername() . " a ajout un suggestion");
            $url = $this->generateUrl('suggestions_get_one', array('id' => $suggestion->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
        }
        $notification->setLink($url);
        $notification->setVisualized(true);
        $this->em->persist($notification);
        $this->em->flush();
    }

}