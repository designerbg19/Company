<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleResponse;
use App\Form\ArticleResponseType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package  App\Controller
 * @Route("/api", name="articleResponse_api")
 */
class ArticleResponseController extends MainController
{
    /**
     * @Route("/articleResponse", name="get_all_article_response")
     */
    public function index()
    {
        $articlesResponse = $this->em->getRepository(ArticleResponse::class)->findAll();
        if(isset($articlesResponse)) {
            return $this->successResponse($articlesResponse);
        }
    }

    /**
     * @Route("/articleResponse/{id}", name="get_one_article_response")
     */
    public function show($id)
    {
        $articlesResponse = $this->em->getRepository(ArticleResponse::class)->find($id);
        if(isset($articlesResponse)) {
            return $this->successResponse($articlesResponse);
        }
    }

    /**
     * @Route("/articleResponses", name="create_articleResponse", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $articlesResponse = new ArticleResponse();
        try {
            $persistService->insert($request,ArticleResponseType::class,$articlesResponse,$data);
            $articlesResponse->setUser($this->getUser());
            if(isset($data['article'])) {
                $legalStatus = $this->em->getRepository(Article::class)->find($data['article']);
                $articlesResponse->setArticle($legalStatus);
            }
            $articlesResponse->setUpdatedAt(new \DateTime());
            $this->em->persist($articlesResponse);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "id"=> $articlesResponse->getId()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/articleResponses/{id}", name="edit_articleResponse", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $articlesResponse = $this->em->getRepository(ArticleResponse::class)->find($id);
        try {
            $persistService->update($request,ArticleResponseType::class,$articlesResponse,$data);
            $articlesResponse->setUser($this->getUser());
            if(isset($data['article'])) {
                $legalStatus = $this->em->getRepository(Article::class)->find($data['article']);
                $articlesResponse->setArticle($legalStatus);
            }
            $articlesResponse->setUpdatedAt(new \DateTime());
            $this->em->persist($articlesResponse);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "id"=> $articlesResponse->getId()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/articleResponses/{id}", name="delete_articleResponses", methods={"DELETE"})
     */
    public function delete($id)
    {
        $articlesResponse = $this->em->getRepository(ArticleResponse::class)->find($id);
        if(isset($articlesResponse)) {
            $this->em->remove($articlesResponse);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'response successfully delete']);
        }
    }
}
