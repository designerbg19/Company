<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 *  @package App\Controller
 * @Route("/api", name="questionnaire_api")
 */
class QuestionnaireController extends MainController
{
    /**
     * @Route("/questionnaires", name="get_all_questionnaire", methods={"GET"})
     */
    public function index()
    {
        $questionnaire = $this->em->getRepository(Questionnaire::class)->findAll();
        if(isset($questionnaire)) {
            return $this->successResponse($questionnaire);
        }
    }

    /**
     * @Route("/questionnaires/{id}", name="get_one_questionnaire" , methods={"GET"})
     */
    public function show($id)
    {
        $questionnaire = $this->em->getRepository(Questionnaire::class)->find($id);
        if(isset($questionnaire)) {
            return $this->successResponse($questionnaire);
        }
    }

    /**
     * @Route("/questionnaires", name="create_questionnaire", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $questionnaire = new Questionnaire();
        try {
            $persistService->insert($request,QuestionnaireType::class,$questionnaire,$data);
            if(isset($data['profiles'])) {
                $profiles = $data['profiles'];
                foreach ($profiles as $value) {
                    $profile = $this->em->getRepository(Profile::class)->find($value);
                    $questionnaire->addProfiles($profile);
                }
            }
            $this->em->persist($questionnaire);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "questionnaire successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @param array $profiles
     * @param Questionnaire $param
     */
    private function removeOldProfile(array $profiles,Questionnaire $param)
    {
        foreach ($profiles as $profile) {
            $oldProfile = $this->em->getRepository(Profile::class)->find($profile['id']);
            $param->removeProfiles($oldProfile);
            $this->em->persist($param);
            $this->em->flush();
        }
    }

    /**
     * @Route("/questionnaires/{id}", name="edit_questionnaire" , methods={"POST","PUT"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $questionnaire = $this->em->getRepository(Questionnaire::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,QuestionnaireType::class,$questionnaire,$data);
            if(isset($data['profiles'])) {
                $profilesByQuestionnaire = $this->em->getRepository(Questionnaire::class)->findProfilesByQuestionnaire($id);
                $this->removeOldProfile($profilesByQuestionnaire,$questionnaire);
                $profiles = $data['profiles'];
                foreach ($profiles as $value) {
                    $profile = $this->em->getRepository(Profile::class)->find($value);
                    $questionnaire->addProfiles($profile);
                }
            }
            $this->em->persist($questionnaire);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "questionnaire successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/questionnaires", name="delete_questionnaire", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $questionnaire = $this->em->getRepository(Questionnaire::class)->find($id);
            $this->em->remove($questionnaire);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "questionnaire successfully deleted"]);
    }
}
