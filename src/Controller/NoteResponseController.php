<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Note;
use App\Entity\NoteResponses;
use App\Entity\User;
use App\Form\NoteResponsesType;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="noteResponses_api")
 */
class NoteResponseController extends MainController
{
    /**
     * @Route("/noteResponses", name="noteResponses_get_all", methods={"GET"})
     */
    public function index()
    {
        $noteResponses = $this->em->getRepository(NoteResponses::class)->findAll();
        $data = $this->getData($noteResponses);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/noteResponses/{id}", name="noteResponses_get_one", methods={"GET"})
     */
    public function show($id)
    {
        $noteResponse = $this->em->getRepository(NoteResponses::class)->findBy(['id'=>$id]);
        $data = $this->getData($noteResponse);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/noteResponses/{idNote}", name="create_noteResponses", methods={"POST"})
     */
    public function create(Request $request,$idNote,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $note =  $this->em->getRepository(Note::class)->find($idNote);
        try {
            $noteResponse = new NoteResponses();
            $persistService->insert($request,NoteResponsesType::class,$noteResponse,$data);
            $noteResponse->setUser($this->getUser());
            $noteResponse->setNote($note);
            $note->addNoteResponses($noteResponse);
            if(isset($data['respondTo'])) {
                $user = $this->em->getRepository(User::class)->find($data['respondTo']);
                $this->notification($user,$noteResponse,null,$request,$persistService);
                $noteResponse->setRespondTo($user);
            }
            $this->em->persist($noteResponse);
            $this->em->persist($note);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'noteResponse successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/noteResponse/{id}", name="edit_noteResponses", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $noteResponse =  $this->em->getRepository(NoteResponses::class)->find($id);
        try {
            $persistService->update($request,NoteResponsesType::class,$noteResponse,$data);
            $this->em->persist($noteResponse);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'noteResponse successfully edited']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/noteResponses", name="delete_noteResponse", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $noteResponse = $this->em->getRepository(NoteResponses::class)->find($id);
            $this->em->remove($noteResponse);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'noteResponse successfully deleted']);
    }

    private function getData(array $notes): array
    {
        $data = [];
        foreach ($notes as $value) {
            $company = $this->em->getRepository(Company::class)->find($value->getNote()->getCompany());
            $data[] = [
                'id' => $value->getId(),
                'user' => array('id' => $value->getUser()->getId(), 'username' => $value->getUser()->getUsername(), 'email' => $value->getUser()->getEmail()),
                'company' => [
                    'id' => $company->getId(),
                    'name' => $company->getName(),
                ],
                'note' => $value->getNote()->getId(),
                'message' => $value->getMessage(),
                'published' => $value->getPublished(),
                'created_at' => $value->getCreatedAt(),
            ];
        }
        foreach ($data as $key => $d) {
            $respondTo = $this->em->getRepository(NoteResponses::class)->findLNoteResponsesByUser($d['id']);
            if (isset($respondTo)) {
                foreach ($respondTo as $value) {
                    $data[$key]['respondTo'][] = [
                        'id' => $value['id'],
                        'username' => $value['username'],
                        'email' => $value['email'],
                    ];
                }
            }
        }
        return $data;
    }
}
