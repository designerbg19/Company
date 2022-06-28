<?php

namespace App\Controller;

use App\Entity\LegalStatus;
use App\Form\LegalStatusType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="legal_status_api")
 */
class LegalStatusController extends MainController
{
    /**
     * @Route("/legalStatus", name="get_all_legalStatus", methods={"GET"})
     */
    public function index()
    {
        $legalStatus = $this->em->getRepository(LegalStatus::class)->findAll();
        if(isset($legalStatus)) {
            return $this->successResponse($legalStatus);
        }
    }

    /**
     * @Route("/legalStatus/{id}", name="get_one_legalStatus", methods={"GET"})
     */
    public function show(int $id)
    {
        $legalStatus = $this->em->getRepository(LegalStatus::class)->find($id);
        if(isset($legalStatus)) {
            return $this->successResponse($legalStatus);
        }
    }

    /**
     * @Route("/legalStatus", name="create_legalStatus", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $legalStatus = new LegalStatus();
        try {
            $persistService->insert($request,LegalStatusType::class,$legalStatus,$data);
            $this->em->persist($legalStatus);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'legalStatus successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/legalStatus/{id}", name="edit_legalStatus", methods={"POST","PUT"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $legalStatus = $this->em->getRepository(LegalStatus::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,LegalStatusType::class,$legalStatus,$data);
            $this->em->persist($legalStatus);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'legalStatus successfully edited']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/legalStatus", name="delete_legalStatus", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $legalStatus = $this->em->getRepository(LegalStatus::class)->find($id);
            $this->em->remove($legalStatus);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'legalStatus successfully deleted']);
    }
}
