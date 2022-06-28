<?php

namespace App\Controller;

use App\Entity\Naf;
use App\Form\NafType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="naf_api")
 */
class NafController extends MainController
{
    /**
     * @Route("/nafs", name="get_all_naf", methods={"GET"})
     */
    public function index()
    {
        $nafs = $this->em->getRepository(Naf::class)->findAll();
        if(isset($nafs)) {
            return $this->successResponse($nafs);
        }
    }

    /**
     * @Route("/nafs/{id}", name="get_one_naf", methods={"GET"})
     */
    public function show($id)
    {
        $naf = $this->em->getRepository(Naf::class)->find($id);
        if(isset($naf)) {
            return $this->successResponse($naf);
        }
    }

    /**
     * @Route("/nafs", name="create_nafs", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $naf = new Naf();
        try {
            $persistService->insert($request,NafType::class,$naf,$data);
            $this->em->persist($naf);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'naf successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/nafs/{id}", name="edit_nafs", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $naf = $this->em->getRepository(Naf::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,NafType::class,$naf,$data);
            $this->em->persist($naf);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'naf successfully edited']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/nafs", name="delete_naf", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $naf = $this->em->getRepository(Naf::class)->find($id);
            $this->em->remove($naf);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'naf successfully deleted']);
    }
}
