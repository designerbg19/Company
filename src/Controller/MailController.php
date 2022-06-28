<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Form\MailType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="mail_api")
 */
class MailController extends MainController
{
    /**
     * @Route("/mails", name="get_all_mails", methods={"GET"})
     */
    public function index()
    {
        $mails = $this->em->getRepository(Mail::class)->findAll();
        if(isset($mails)) {
            return $this->successResponse($mails);
        }
    }

    /**
     * @Route("/mails/{id}", name="get_get_mail", methods={"GET"})
     */
    public function show($id)
    {
        $mail = $this->em->getRepository(Mail::class)->find($id);
        if(isset($mail)) {
            return $this->successResponse($mail);
        }
    }

    /**
     * @Route("/mails", name="create_mail", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $mail = new Mail();
        try {
            $persistService->insert($request,MailType::class,$mail,$data);
            $this->em->persist($mail);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'mail successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/mails/{id}", name="edit_mail", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $mail = $this->em->getRepository(Mail::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,MailType::class,$mail,$data);
            $this->em->persist($mail);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'mail successfully edited']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/mails", name="delete_mail", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $mail = $this->em->getRepository(Mail::class)->find($id);
            $this->em->remove($mail);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'mail successfully deleted']);
        }
    }
}
