<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleResponse;
use App\Form\ArticleType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package  App\Controller
 * @Route("/api", name="article_api")
 */
class ArticleController extends MainController
{
    /**
     * @Route("/articles", name="get_all_article", methods={"GET"})
     */
    public function index()
    {
        $articles = $this->em->getRepository(Article::class)->findAll();
        $data = $this->getData($articles);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/articles/{id}", name="get_one_article", methods={"GET"})
     */
    public function show($id)
    {
        $article = $this->em->getRepository(Article::class)->findBy(['id' =>$id]);
        $data = $this->getData($article);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }
    /**
     * @param array $articles
     * @return array
     */
    public function getData(array $articles): array
    {
        $data = [];
        foreach ($articles as $value) {
            $data[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'news' => $value->getNews(),
                'abstract' => $value->getAbstract(),
                'createdDate' => $value->getCreatedDate(),
                'published' => $value->getPublished(),
                'video' => $value->getVideo(),
                'logo' => $value->getLogo(),
                'image' => $value->getImage(),
            ];
        }
        foreach ($data as $key => $d) {
            if (!empty($d['id'])) {
                $articleResponses = $this->em->getRepository(ArticleResponse::class)->findBy(['article'=>$d['id']]);
                if (count($articleResponses) > 0) {
                    foreach ($articleResponses as $articleResponse) {
                        $data[$key]['articleResponse'][] = [
                            'id' => $articleResponse->getId(),
                            'message' => $articleResponse->getMessage(),
                            'published' => $articleResponse->getPublished(),
                            'updatedAt' => $articleResponse->getUpdatedAt(),
                            'user' => array('id'=>$articleResponse->getUser()->getId(),'email'=>$articleResponse->getUser()->getEmail(),'username'=>$articleResponse->getUser()->getUserName())
                        ];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @Route("/articles", name="create_articles", methods={"POST"})
     */
    public function create(Request $request,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $article = new Article();
        try {
            $persistService->insert($request,ArticleType::class,$article,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $article->setImage($image);
                }
                if(isset($file['logo'])){
                    $logo = $fileUploader->ImageUploade(array($file['logo']), $this->em);
                    $article->setLogo($logo);
                }
            }
            $article->setCreatedDate(new \DateTime());
            $this->em->persist($article);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "id"=> $article->getId()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/articles/{id}", name="edit_articles", methods={"POST"})
     */
    public function update(Request $request,$id,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $article = $this->em->getRepository(Article::class)->find($id);
        try {
            $persistService->update($request,ArticleType::class,$article,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $article->setImage($image);
                }
                if(isset($file['logo'])){
                    $logo = $fileUploader->ImageUploade(array($file['logo']), $this->em);
                    $article->setLogo($logo);
                }
            }
            $this->em->persist($article);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "id"=> $article->getId()]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/articles", name="delete_article", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $article = $this->em->getRepository(Article::class)->find($id);
            $this->em->remove($article);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'article successfully delete']);
    }
}
