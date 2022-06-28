<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Sector;
use App\Entity\Suggestion;
use App\Entity\Tag;
use App\Form\SuggestionType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="suggestion_api")
 */
class SuggestionController extends MainController
{
    /**
     * @Route("/suggestions", name="suggestions_get_all", methods={"GET"})
     */
    public function index()
    {
        $suggestions = $this->em->getRepository(Suggestion::class)->findAll();
        $data = $this->getData($suggestions);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/suggestions/{id}", name="suggestions_get_one", methods={"GET"})
     */
    public function show($id)
    {
        $suggestion = $this->em->getRepository(Suggestion::class)->findBy(['id' => $id]);
        $data = $this->getData($suggestion);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }
    /**
     * @return array
     */
    public function getData(array $suggestions): array
    {
        $data = [];
        foreach ($suggestions as $value) {
            $data[] = [
                'id' => $value->getId(),
                'message' => $value->getMessage(),
                'description' => $value->getDescription(),
                'vuStatus' => $value->getVuStatus(),
                'logo' => $value->getLogo(),
                'user' => array('id' => $value->getUser()->getId(),'username' => $value->getUser()->getUsername(),'email' => $value->getUser()->getEmail())
            ];
        }
        foreach ($data as $key => $d) {
            $company = $this->em->getRepository(Suggestion::class)->findCompanyBySuggestion($d['id']);
            if (isset($company)) {
                foreach ($company as $value) {
                    $data[$key]['company'][] = [
                        'id' => $value['id'],
                        'name' => $value['name'],
                    ];
                }
            }
            $tags = $this->em->getRepository(Suggestion::class)->findTagsBySuggestion($d['id']);
            if (isset($tags)) {
                foreach ($tags as $value) {
                    $data[$key]['tags'][] = [
                        'id' => $value['id'],
                        'name' => $value['name'],
                    ];
                }
            }
            $sectors = $this->em->getRepository(Suggestion::class)->findSectorBySuggestion($d['id']);
            if (isset($sectors)) {
                foreach ($sectors as $value) {
                    $data[$key]['sectors'][] = [
                        'id' => $value['id'],
                        'name' => $value['name'],
                    ];
                }
            }
        }
        return $data;
    }
    /**
     * @Route("/suggestions", name="create_suggestion", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $suggestion = new Suggestion();
        try {
            $persistService->insert($request,SuggestionType::class,$suggestion,$data);
            if(isset($data['company'])) {
                $company = $this->em->getRepository(Company::class)->find($data['company']);
                if ($company === null) {
                    return $this->successResponse(["code" => 409, "message" => 'company not exist']);
                }
                $suggestion->setCompany($company);
            }
            if(isset($data['tags'])) {
                $tags = $data['tags'];
                foreach ($tags as $value) {
                    $tag = $this->em->getRepository(Tag::class)->find($value);
                    $suggestion->addTags($tag);
                }
            }
            if(isset($data['sectors'])) {
                $sectors = $data['sectors'];
                foreach ($sectors as $value) {
                    $sector = $this->em->getRepository(Sector::class)->find($value);
                    $suggestion->addSectors($sector);
                }
            }
            $suggestion->setVuStatus(false);
            $suggestion->setUser($this->getUser());
            $this->em->persist($suggestion);
            $this->em->flush();
            $this->notification($this->getUser(),null,$suggestion,$request,$persistService);
            return $this->successResponse(["code" => 200, "message"=> "suggestion successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/suggestions/{id}", name="edit_suggestion", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $suggestion = $this->em->getRepository(Suggestion::class)->find($id);
        try {
            $persistService->update($request,SuggestionType::class,$suggestion,$data);
            if(isset($data['company'])) {
                $company = $this->em->getRepository(Company::class)->find($data['company']);
                $suggestion->setCompany($company);
            }
            if(isset($data['tags'])) {
                $tags = $data['tags'];
                foreach ($tags as $value) {
                    $tag = $this->em->getRepository(Tag::class)->find($value);
                    $suggestion->addTags($tag);
                }
            }
            if(isset($data['sectors'])) {
                $sectors = $data['sectors'];
                foreach ($sectors as $value) {
                    $sector = $this->em->getRepository(Sector::class)->find($value);
                    $suggestion->addSectors($sector);
                }
            }
            $this->em->persist($suggestion);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message"=> "suggestion successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/reportProblem", name="create_report_problem", methods={"POST"})
     */
    public function reportProblem(Request $request)
    {
        $data = $this->jsonDecode($request);
        $suggestion = new Suggestion();
        try {
            if (isset($data['message'])) {
                $suggestion->setMessage($data['message']);
            } else {
                return $this->successResponse(["code" => 409, "message" => 'to need to create message']);
            }
            if (isset($data['company'])) {
                $company = $this->em->getRepository(Company::class)->find($data['company']);
                if ($company === null) {
                    return $this->successResponse(["code" => 409, "message" => 'company not exist']);
                }
                $suggestion->setCompany($company);
            }
            $suggestion->setVuStatus(false);
            $suggestion->setUser($this->getUser());
            $this->em->persist($suggestion);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "report a Problem successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()], 409);
        }
    }

    /**
     * @Route("/suggestionsLogo/{idCompany}", name="logo_suggestion_create", methods={"POST"})
     */
    public function logoSuggestion(Request $request,$idCompany,FileUploader $fileUploader)
    {
        $suggestion = new Suggestion();
        try {
            $company = $this->em->getRepository(Company::class)->find($idCompany);
            if ($company === null) {
                return $this->successResponse(["code" => 409, "message" => 'company not exist']);
            }
            $file = $fileUploader->upload($request);
            if($file != null){
                $image = $fileUploader->ImageUploade($file, $this->em);
                $suggestion->setLogo($image);
            }
            $suggestion->setCompany($company);
            $suggestion->setVuStatus(false);
            $suggestion->setUser($this->getUser());
            $this->em->persist($suggestion);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "suggestions Logo successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()], 409);
        }
    }

    /**
     * @Route("/vuStatus/{id}", name="edit_status", methods={"POST"})
     */
    public function updateStatus(int $id)
    {
        $suggestion = $this->em->getRepository(Suggestion::class)->find($id);
        $suggestion->setVuStatus(true);
        $this->em->persist($suggestion);
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message"=> "status successfully edited"]);
    }
    /**
     * @Route("/suggestions", name="delete_suggestions", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $suggestion = $this->em->getRepository(Suggestion::class)->find($id);
            $this->em->remove($suggestion);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message"=> "suggestion successfully deleted"]);
    }
}
