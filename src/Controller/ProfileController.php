<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="profile_api")
 */
class ProfileController extends MainController
{
    /**
     * @Route("/profiles", name="get_all_profiles", methods={"GET"})
     */
    public function index()
    {
        $profiles = $this->em->getRepository(Profile::class)->findAll();
        $data = $this->getData($profiles);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/profiles/{id}", name="get_one_profile", methods={"GET"})
     */
    public function show(int $id)
    {
        $profile = $this->em->getRepository(Profile::class)->findBy(['id'=>$id]);
        $data = $this->getData($profile);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/profiles", name="profile_create", methods={"POST"})
     */
    public function create(Request $request,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $profile = new Profile();
        try {
            $persistService->insert($request,ProfileType::class,$profile,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $profile->setImage($image);
                }
                if(isset($file['photo'])){
                    $photo = $fileUploader->ImageUploade(array($file['photo']), $this->em);
                    $profile->setPhoto($photo);
                }
            }
            $this->em->persist($profile);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "profile successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/profiles/{id}", name="profile_update", methods={"PATCH","POST"})
     */
    public function edit(Request $request,$id,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $profile = $this->em->getRepository(Profile::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,ProfileType::class,$profile,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $profile->setImage($image);
                }
                if(isset($file['photo'])){
                    $photo = $fileUploader->ImageUploade(array($file['photo']), $this->em);
                    $profile->setPhoto($photo);
                }
            }
            $this->em->persist($profile);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "profile successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/profiles", name="delete_profiles", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $profile = $this->em->getRepository(Profile::class)->find($id);
            $this->em->remove($profile);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "profile successfully deleted"]);
    }

    /**
     * @param array $profiles
     * @return array
     */
    public function getData(array $profiles): array
    {
        $data = [];
        foreach ($profiles as $value) {
            $data[] = [
                'id' => $value->getId(),
                'name' => $value->getName(),
                'description' => $value->getDescription(),
                'image' => $value->getImage(),
                'photo' => $value->getPhoto(),
            ];
        }
        return $data;
    }
}
