<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="tag_api")
 */
class TagController extends MainController
{
    /**
     * @Route("/tags", name="get_all_tags" , methods={"GET"})
     */
    public function index()
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();
        if(isset($tags)) {
            return $this->successResponse($tags);
        }
    }

    /**
     * @Route("/tags/{id}", name="get_one_tags" , methods={"GET"})
     */
    public function show($id)
    {
        $tag = $this->em->getRepository(Tag::class)->find($id);
        if(isset($tag)) {
            return $this->successResponse($tag);
        }
    }
    /**
     * @Route("/tags", name="create_tags", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $tag = new Tag();
        try {
            $persistService->insert($request,TagType::class,$tag,$data);
            $this->em->persist($tag);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "tag successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/tags/{id}", name="edit_tags", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $tag = $this->em->getRepository(Tag::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,TagType::class,$tag,$data);
            $this->em->persist($tag);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "tag successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/tags", name="delete_tags", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $tag = $this->em->getRepository(Tag::class)->find($id);
            $this->em->remove($tag);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "tag successfully deleted"]);
    }
}
