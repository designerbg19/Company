<?php

namespace App\Controller;

use App\Entity\Notifications;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="notifications_api")
 */
class NotificationsController extends MainController
{
    /**
     * @Route("/notifications", name="notifications_get_all", methods={"GET"})
     */
    public function index()
    {
        $notifications = $this->em->getRepository(Notifications::class)->findAll();
        $data = $this->getData($notifications);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/notifications/{id}", name="notifications_get_one", methods={"GET"})
     */
    public function show($id)
    {
        $notification = $this->em->getRepository(Notifications::class)->findBy(['id' => $id]);
        $data = $this->getData($notification);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/notificationsByUser/{idUser}", name="notifications_get_by_user", methods={"GET"})
     */
    public function showByUser($idUser)
    {
        $notificationUser = $this->em->getRepository(Notifications::class)->findBy(['user' => $idUser]);
        $data = $this->getData($notificationUser);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/notificationsVisualized/{id}", name="edit_visualized", methods={"POST"})
     */
    public function create(Request $request,$id)
    {
        $notification = $this->em->getRepository(Notifications::class)->find($id);
        try {
            if($notification->getVisualized()){
                $notification->setVisualized(false);
            }else {
                $notification->setVisualized(true);
            }
            $this->em->persist($notification);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "notifications successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/notifications", name="notifications_delete", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $notification = $this->em->getRepository(Notifications::class)->find($id);
            $this->em->remove($notification);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'notifications successfully deleted']);
    }

    /**
     * @param array $notifications
     * @return array
     */
    public function getData(array $notifications): array
    {
        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'visualized' => $notification->getVisualized(),
                'link' => $notification->getLink(),
                'date' => $notification->getDate(),
                'user' => array('id' => $notification->getUser()->getId(), 'username' => $notification->getUser()->getUsername(), 'email' => $notification->getUser()->getEmail())
            ];
        }
        return $data;
    }
}
