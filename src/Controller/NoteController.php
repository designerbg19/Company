<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Notation;
use App\Entity\Note;
use App\Entity\NoteNotation;
use App\Entity\NoteQuestionnaire;
use App\Entity\NoteResponses;
use App\Entity\Profile;
use App\Entity\Questionnaire;
use App\Entity\User;
use App\Form\NoteResponsesType;
use App\Form\NoteType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @package App\Controller
 * @Route("/api", name="note_api")
 */
class NoteController extends MainController
{
    /**
     * @Route("/notes", name="get_all_notes", methods={"GET"})
     */
    public function index()
    {
        $notes = $this->em->getRepository(Note::class)->findAll();
        $data = $this->getNotes($notes);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/notes/{id}", name="get_one_notes", methods={"GET"})
     */
    public function show($id)
    {
        $notes = $this->em->getRepository(Note::class)->findBy(['id'=>$id]);
        $data = $this->getNotes($notes);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/notes", name="create_note", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $note = new Note();
        $user = $this->getUser();
        try {
            $persistService->insert($request,NoteType::class,$note,$data);
            if(isset($data['notePublicQuestions'])) {
                $notations = $data['notePublicQuestions'];
                foreach ($notations as $value) {
                    $noteNotation = new NoteNotation();
                    $noteNotation->setValue($value['value']);
                    $stack[] = $value['value'];

                    $notation = $this->em->getRepository(Notation::class)->find($value['id']);
                    $noteNotation->setNotation($notation);
                    $noteNotation->setNote($note);
                    $this->em->persist($noteNotation);
                    $note->addNoteNotation($noteNotation);
                }
            }
            $socre = array_sum($stack);
            $note->setScore($socre/5);
            if(isset($data['noteStoredQuestions'])) {
                $questionnaires = $data['noteStoredQuestions'];
                foreach ($questionnaires as $value) {
                    $noteQuestionnaire = new NoteQuestionnaire();
                    $noteQuestionnaire->setValue($value['value']);
                    $questionnaire = $this->em->getRepository(Questionnaire::class)->find($value['id']);
                    $noteQuestionnaire->setQuestionnaire($questionnaire);
                    $noteQuestionnaire->setNote($note);
                    $this->em->persist($noteQuestionnaire);
                    $note->addNoteQuestionnaire($noteQuestionnaire);
                }
            }
            if(isset($data['responseUser'])) {
                $userResponses = $data['responseUser'];
                foreach ($userResponses as $value) {
                    $noteResponse = new NoteResponses();
                    $persistService->insert($request,NoteResponsesType::class,$noteResponse,$value);
                    $noteResponse->setUser($user);
                    $noteResponse->setNote($note);
                    $this->em->persist($noteResponse);
                    $note->addNoteResponses($noteResponse);
                }
            }
            $noteByUser = $this->em->getRepository(Note::class)->findNoteByUser($user,$data['company']);
            $notes = array_shift($noteByUser) ;
            if (count($noteByUser) < 1 || (count($noteByUser) == 1 && $notes->getProfile()->getId() != $data['profile'])){
                $note->setUser($user);
                if(isset($data['profile'])) {
                    $profile = $this->em->getRepository(Profile::class)->find($data['profile']);
                    $note->setProfile($profile);
                }
                if(isset($data['company'])) {
                    $company = $this->em->getRepository(Company::class)->find($data['company']);
                    $note->setCompany($company);
                }
                $this->em->persist($note);
                $this->em->flush();
                return $this->successResponse(["code" => 200, "message" => 'note successfully added']);
            } else {
                return $this->successResponse(["code" => 409, "message" => "you already have notes"],409);
            }
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/notes/{id}", name="edit_notes", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data =  $this->jsonDecode($request);
        $note = $this->em->getRepository(Note::class)->findOneBy(['id' => $id]);
        $getNotations = $this->em->getRepository(Note::class)->findNotatonByNote($id);
        $getQuestionnaire = $this->em->getRepository(Note::class)->findQuestionnaireByNote($id);
        $getResponses = $this->em->getRepository(Note::class)->findResponseByNote($id);
        try {
            if(isset($data['notePublicQuestions'])) {
                $notations = $data['notePublicQuestions'];
                foreach ($notations as $value) {
                    foreach ($getNotations as $notes) {
                        $noteNotation = $this->em->getRepository(NoteNotation::class)->findOneBy(['id' => $notes['id']]);
                        $notation = $this->em->getRepository(Notation::class)->findOneBy(['id' => $notes['notation_id']]);
                        if($notes['notation_id'] === $value['id']){
                            $noteNotation->setNotation($notation);
                            $noteNotation->setNote($note);
                            $noteNotation->setValue($value['value']);
                            $this->em->persist($noteNotation);
                            $note->addNoteNotation($noteNotation);
                        }
                    }
                }
            }
            $persistService->update($request,NoteType::class,$note,$data);
            if(isset($data['noteStoredQuestions'])) {
                $questionnaires = $data['noteStoredQuestions'];
                foreach ($questionnaires as $value) {
                    foreach ($getQuestionnaire as $question) {
                        $noteQuestionnaire = $this->em->getRepository(NoteQuestionnaire::class)->findOneBy(['id' => $question['id']]);
                        $questionnaire = $this->em->getRepository(Questionnaire::class)->find($question['questionnaire_id']);
                        if($question['questionnaire_id'] === $value['id']) {
                            $noteQuestionnaire->setQuestionnaire($questionnaire);
                            $noteQuestionnaire->setNote($note);
                            $noteQuestionnaire->setValue($value['value']);
                            $this->em->persist($noteQuestionnaire);
                            $note->addNoteQuestionnaire($noteQuestionnaire);
                        }
                    }
                }
            }
            if (isset($data['responseUser'])) {
                $userResponses = $data['responseUser'];
                foreach ($userResponses as $value) {
                    foreach ($getResponses as $response) {
                        $noteResponse = $this->em->getRepository(NoteResponses::class)->findOneBy(['id' => $response['id']]);
                        $user = $this->em->getRepository(User::class)->find($data['user']);
                        $persistService->update($request,NoteResponsesType::class,$noteResponse,$value);
                        $noteResponse->setUser($user);
                        $noteResponse->setNote($note);
                        $this->em->persist($noteResponse);
                        $note->addNoteResponses($noteResponse);

                    }
                }
            }
            $noteByUser = $this->em->getRepository(Note::class)->findNoteByUser($data['user'],$data['company']);
            if (count($noteByUser) > 0){
                if(isset($data['user'])) {
                    $user = $this->em->getRepository(User::class)->find($data['user']);
                    $note->setUser($user);
                }
                if(isset($data['profile'])) {
                    $profile = $this->em->getRepository(Profile::class)->find($data['profile']);
                    $note->setProfile($profile);
                }
                if(isset($data['company'])) {
                    $company = $this->em->getRepository(Company::class)->find($data['company']);
                    $note->setCompany($company);
                }
                $this->em->persist($note);
                $this->em->flush();
                return $this->successResponse(["code" => 200, "message" => 'note successfully edited']);
            } else {
                return $this->successResponse(["code" => 409, "message" => "you already have notes"],409);
            }
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/notes", name="delete_notes", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $note = $this->em->getRepository(Note::class)->find($id);
            $this->em->remove($note);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'note successfully deleted']);
    }

    /**
     * @param array $data
     * @param $noteQuestionnaire
     * @return array
     */
    private function getNotes(array $notes): array
    {
        $data = [];
        foreach ($notes as $value) {
            $data[] = [
                'id' => $value->getId(),
                'user' => array('id' => $value->getUser()->getId(),'username' =>$value->getUser()->getUsername(),'email' =>$value->getUser()->getEmail(),'image' => $value->getUser()->getImages()),
                'company' => array('id' => $value->getCompany()->getId(),'name' =>$value->getCompany()->getName(),'address' =>$value->getCompany()->getAddress(),'image' => $value->getCompany()->getImage()),
                'description' => $value->getDescription(),
                'score' => $value->getScore(),
                'content' => $value->getContent(),
                'activeProfile' => $value->getActiveProfile(),
                'underProfile' => $value->getUnderProfile(),
                'startDate' => $value->getStartDate(),
                'endDate' => $value->getEndDate(),
                'profile' => $value->getProfile()->getName(),
            ];
        }
        foreach ($data as $key => $d) {
            //TODO:: getNoteNotations by ID 19 => 1 , 2
            if (!empty($d['id']) && $d['id'] != null) {
                $noteNotations = $this->em->getRepository(NoteNotation::class)->findBy(['note' => $d['id']]);
                if (count($noteNotations) > 1) {
                    foreach ($noteNotations as $noteNotation) {
                        $data[$key]['noteNotation'][] = [
                            'value' => $noteNotation->getValue(),
                            'notation' => $noteNotation->getNotation()->getId(),
                        ];
                    }
                } elseif (count($noteNotations) === 1) {
                    $noteNotation = $noteNotations[0];
                    $data[$key]['noteNotation'][] = [
                        'value' => $noteNotation->getValue(),
                        'notationId' => $noteNotation->getNotation()->getId(),
                    ];
                } else {
                    //TODO:: Messege is the array empty
                }
            }
            //}
            //foreach ($data as $key=>$d) {
            //TODO:: getNoteQuestionnaire by ID
            if (!empty($d['id']) && $d['id'] != null) {
                $noteQuestionnaires = $this->em->getRepository(NoteQuestionnaire::class)->findBy(['note' => $d['id']]);
                if (count($noteQuestionnaires) > 1) {
                    foreach ($noteQuestionnaires as $noteQuestionnaire) {
                        $data[$key]['noteQuestionnaire'][] = [
                            'value' => $noteQuestionnaire->getValue(),
                            'questionnaireId' => $noteQuestionnaire->getQuestionnaire()->getId(),
                        ];
                    }
                } elseif (count($noteQuestionnaires) === 1) {
                    $noteNotation = $noteQuestionnaire[0];
                    $data[$key]['noteQuestionnaire'][] = [
                        'value' => $noteNotation->getValue(),
                        'questionnaireId' => $noteQuestionnaire->getQuestionnaire()->getId(),
                    ];
                } else {
                    //TODO:: Messege is the array empty
                }
            }
            //}
            //foreach ($data as $key=>$d) {
            //TODO:: getNoteResponses by ID
            if (!empty($d['id']) && $d['id'] != null) {
                $noteResponses = $this->em->getRepository(NoteResponses::class)->findBy(['note' => $d['id']]);
                if (count($noteResponses) > 1) {
                    foreach ($noteResponses as $noteResponse) {
                        $data[$key]['noteResponse'][] = [
                            'message' => $noteResponse->getMessage(),
                            'published' => $noteResponse->getPublished(),
                        ];
                    }
                } elseif (count($noteResponses) === 1) {
                    $noteResponse = $noteResponses[0];
                    $data[$key]['noteResponse'][] = [
                        'message' => $noteResponse->getMessage(),
                        'published' => $noteResponse->getPublished(),
                    ];
                } else {
                    //TODO:: Messege is the array empty
                }
            }
        }
        return $data;
    }
}
