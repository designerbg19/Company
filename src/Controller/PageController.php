<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package  App\Controller
 * @Route("/api", name="page_api")
 */
class PageController extends MainController
{
    /**
     * @Route("/pages", name="get_all_pages", methods={"GET"})
     */
    public function index()
    {
        $pages = $this->em->getRepository(Page::class)->findAll();
        if(isset($pages)) {
            return $this->successResponse($pages);
        }
    }

    /**
     * @Route("/pages/{id}", name="get_one_pages", methods={"GET"})
     */
    public function show($id)
    {
        $page = $this->em->getRepository(Page::class)->find($id);
        if(isset($page)) {
            return $this->successResponse($page);
        }
    }

    /**
     * @Route("/pages", name="create_page", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $page = new Page();
        try {
            $persistService->insert($request,PageType::class,$page,$data);
            $this->em->persist($page);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message"=> "page successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/pages/{id}", name="edit_page", methods={"POST"})
     */
    public function update(Request $request,PersistService $persistService,$id)
    {
        $data = $this->jsonDecode($request);
        $page = $this->em->getRepository(Page::class)->find($id);
        try {
            $persistService->update($request,PageType::class,$page,$data);
            $this->em->persist($page);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message"=> "page successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/pages", name="delete_page", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $page = $this->em->getRepository(Page::class)->find($id);
            $this->em->remove($page);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'page successfully delete']);
    }
}
