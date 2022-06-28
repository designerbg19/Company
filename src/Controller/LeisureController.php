<?php

namespace App\Controller;

use App\Entity\Leisure;
use App\Form\LeisureType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="leisure_api")
 */
class LeisureController extends MainController
{
    /**
     * @Route("/leisures", name="get_all_leisure" , methods={"GET"})
     */
    public function index()
    {
        $leisure = $this->em->getRepository(Leisure::class)->findAll();
        if(isset($leisure)) {
            return $this->successResponse($leisure);
        }
    }

    /**
     * @Route("/leisures/{id}", name="get_one_leisure" , methods={"GET"})
     */
    public function show($id)
    {
        $leisure = $this->em->getRepository(Leisure::class)->find($id);
        if(isset($leisure)) {
            return $this->successResponse($leisure);
        }
    }

    /**
     * @Route("/leisures", name="create_leisure", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $leisure = new Leisure();
        try {
            $persistService->insert($request,LeisureType::class,$leisure,$data);
            $this->em->persist($leisure);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'leisure successfully added' ]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/leisures/{id}", name="edit_leisure", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $leisure = $this->em->getRepository(Leisure::class)->find($id);
        try {
            $persistService->update($request,LeisureType::class,$leisure,$data);
            $this->em->persist($leisure);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'leisure successfully edited' ]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/leisures", name="delete_leisure", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $leisure = $this->em->getRepository(Leisure::class)->find($id);
            $this->em->remove($leisure);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'leisure successfully deleted']);
    }
}
