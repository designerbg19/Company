<?php

namespace App\Controller;

use App\Entity\Macaron;
use App\Form\MacaronType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="macaron_api")
 */
class MacaronController extends MainController
{
    /**
     * @Route("/macarons", name="get_all_macaron" , methods={"GET"})
     */
    public function index()
    {
        $macarons = $this->em->getRepository(Macaron::class)->findAll();
        if(isset($macarons)) {
            return $this->successResponse($macarons);
        }
    }

    /**
     * @Route("/macarons/{id}", name="get_one_macaron" , methods={"GET"})
     */
    public function show($id)
    {
        $macarons = $this->em->getRepository(Macaron::class)->find($id);
        if(isset($macarons)) {
            return $this->successResponse($macarons);
        }
    }

    /**
     * @Route("/macarons", name="create_macaron", methods={"POST"})
     */
    public function create(Request $request,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $macaron = new Macaron();
        try {
            $persistService->insert($request,MacaronType::class,$macaron,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                $image = $fileUploader->ImageUploade($file, $this->em);
                $macaron->setImage($image);
            }
            $this->em->persist($macaron);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'macaron successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/macarons/{id}", name="edit_macaron" , methods={"POST","PUT"})
     */
    public function update(Request $request,$id,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $macaron = $this->em->getRepository(Macaron::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,MacaronType::class,$macaron,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                $image = $fileUploader->ImageUploade($file, $this->em);
                $macaron->setImage($image);
            }
            $this->em->persist($macaron);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'macaron successfully edited']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/macarons", name="delte_macaron", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $macaron = $this->em->getRepository(Macaron::class)->find($id);
            $this->em->remove($macaron);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'macaron successfully deleted']);
    }
}
