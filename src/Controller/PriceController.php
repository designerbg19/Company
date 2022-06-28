<?php

namespace App\Controller;

use App\Entity\Price;
use App\Form\PriceType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="price_api")
 */
class PriceController extends MainController
{
    /**
     * @Route("/prices", name="get_all_prices", methods={"GET"})
     */
    public function index()
    {
        $prices = $this->em->getRepository(Price::class)->findAll();
        if(isset($prices)) {
            return $this->successResponse($prices);
        }
    }

    /**
     * @Route("/prices/{id}", name="get_one_price", methods={"GET"})
     */
    public function show($id)
    {
        $price = $this->em->getRepository(Price::class)->find($id);
        if(isset($price)) {
            return $this->successResponse($price);
        }
    }

    /**
     * @Route("/prices", name="create_price", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $price = new Price();
        try {
            $persistService->insert($request,PriceType::class,$price,$data);
            $this->em->persist($price);
            $this->em->flush();
            return $this->successResponse($data);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/prices/{id}", name="edit_price", methods={"POST","PUT"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $price = $this->em->getRepository(Price::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,PriceType::class,$price,$data);
            $this->em->persist($price);
            $this->em->flush();
            return $this->successResponse($data);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/prices", name="delete_price", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $price = $this->em->getRepository(Price::class)->find($id);
            $this->em->remove($price);
            $this->em->flush();
            return $this->successResponse("deleted");
        }
    }
}
