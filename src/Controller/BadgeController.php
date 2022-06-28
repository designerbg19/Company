<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Form\BadgeType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="badge_api")
 */
class BadgeController extends MainController
{
    /**
     * @Route("/badges", name="get_all_badge", methods={"GET"})
     */
    public function index()
    {
        $badges = $this->em->getRepository(Badge::class)->findAll();
        if(isset($badges)) {
            return $this->successResponse($badges);
        }
    }

    /**
     * @Route("/badges/{id}", name="get_one_badge", methods={"GET"})
     */
    public function show($id)
    {
        $badge = $this->em->getRepository(Badge::class)->find($id);
        if(isset($badge)) {
            return $this->successResponse($badge);
        }
    }

    /**
     * @Route("/badges", name="create_badge", methods={"POST"})
     */
    public function create(Request $request,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $badge = new Badge();
        try {
            $persistService->insert($request,BadgeType::class,$badge,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                $image = $fileUploader->ImageUploade($file, $this->em);
                $badge->setImage($image);
            }
            $this->em->persist($badge);
            $this->em->flush();
            return $this->successResponse(["id"=>$badge->getId(),"label"=>$badge->getName()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/badges/{id}", name="edit_badge", methods={"POST","PUT"})
     */
    public function update(Request $request,$id,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $badge = $this->em->getRepository(Badge::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,BadgeType::class,$badge,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                $oldImage = $badge->getImage();
                $image = $fileUploader->ImageUploade($file, $this->em);
                $badge->setImage($image);
                $this->em->remove($oldImage);
            }
            $this->em->persist($badge);
            $this->em->flush();
            return $this->successResponse(["id"=>$badge->getId(),"label"=>$badge->getName()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/badges", name="delte_badge", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $badge = $this->em->getRepository(Badge::class)->find($id);
            $this->em->remove($badge);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'badges successfully delete']);
    }
}
