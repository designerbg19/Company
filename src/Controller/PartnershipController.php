<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Note;
use App\Entity\Thumbnails;
use App\Form\PartnershipType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="partnership_api")
 */
class PartnershipController extends MainController
{
    /**
     * @Route("/partnerships", name="get_all_partnership",methods={"GET"})
     */
    public function index()
    {
        $partnerships = $this->em->getRepository(Thumbnails::class)->findAll();
        $data = $this->getData($partnerships);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/partnerships/{id}", name="get_one_partnership",methods={"GET"})
     */
    public function show($id)
    {
        $partnership = $this->em->getRepository(Thumbnails::class)->findBy(['id'=>$id]);
        $data = $this->getData($partnership);
        if (isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/partnershipByCompany/{id}", name="get_partnership_company",methods={"GET"})
     */
    public function findByCompany($id)
    {
        $partnership = $this->em->getRepository(Thumbnails::class)->findBy(['company' => $id]);
        if(isset($partnership)) {
            return $this->successResponse($partnership);
        }
    }

    /**
     * @Route("/partnerships", name="create_partnership", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService,FileUploader $fileUploader)
    {
        $data = $this->jsonDecode($request);
        $partnership = new Thumbnails();
        try {
            $persistService->insert($request,PartnershipType::class,$partnership,$data);
            $company = $this->em->getRepository(Company::class)->find($data['company_id']);
            $partnershipId = $this->em->getRepository(Company::class)->find($data['partnership_id']);
            $file = $fileUploader->upload($request);
            if($file != null){
                $result = $fileUploader->ImageUploade($file, $this->em);
                $partnership->setImage($result);
            }
            $partnership->setPartnership($partnershipId);
            $partnership->setCompany($company);
            $this->em->persist($partnership);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "partnerships successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/partnerships/{id}", name="edit_partnership", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService,FileUploader $fileUploader)
    {
        $data = $this->jsonDecode($request);
        $partnership = $this->em->getRepository(Thumbnails::class)->find($id);
        try {
            $persistService->update($request, PartnershipType::class, $partnership, $data);
            if (isset($data['partnership_id'])) {
                $partnershipId = $this->em->getRepository(Company::class)->find($data['partnership_id']);
                $partnership->setPartnership($partnershipId);
            }
            $file = $fileUploader->upload($request);
            if ($file != null) {
                if (isset($file['image'])) {
                    $result = $fileUploader->ImageUploade($file, $this->em);
                    $partnership->setImage($result);
                }
            }
            $this->em->persist($partnership);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "partnerships successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()], 409);
        }
    }

    /**
     * @Route("/partnerships", name="delete_partnership", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $partnership = $this->em->getRepository(Thumbnails::class)->find($id);
            $this->em->remove($partnership);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "partnerships successfully deleted"]);
    }

    /**
     * @param array $partnerships
     * @return array
     */
    public function getData(array $partnerships): array
    {
        $data = [];
        foreach ($partnerships as $value) {
            $partnership = $this->em->getRepository(Company::class)->find($value->getPartnership());
            $company = $this->em->getRepository(Company::class)->find($value->getCompany());
            $data[] = [
                'id' => $value->getId(),
                'title' => $value->getTitle(),
                'description' => $value->getDescription(),
                'image' => $value->getImage(),
                'company' => [
                    'id' => $company->getId(),
                    'name' => $company->getName(),
                ],
                'partnership' => [
                    'id' => $partnership->getId(),
                    'name' => $partnership->getName(),
                ]
            ];
        }
        foreach ($data as $key => $d) {
            if (!empty($d['id']) && $d['id'] != null) {
                $notes = $this->em->getRepository(Note::class)->findNoteByCompany($d['company']['id']);
                if (count($notes) > 0) {
                    $dataNote = [];
                    foreach ($notes as $note) {
                        array_push($dataNote, $note['score']);
                        $data[$key]['company']['note'][] = [
                            'id' => $note['id'],
                            'score' => $note['score'],
                            'date' => $note['createdAt'],
                        ];
                    }
                    $data[$key]['company']['total'] = array_sum($dataNote);
                }
                $notesPartnership = $this->em->getRepository(Note::class)->findNoteByCompany($d['partnership']['id']);
                if (count($notesPartnership) > 0) {
                    $dataNote = [];
                    foreach ($notesPartnership as $note) {
                        array_push($dataNote, $note['score']);
                        $data[$key]['partnership']['note'][] = [
                            'id' => $note['id'],
                            'score' => $note['score'],
                            'date' => $note['createdAt'],
                        ];
                    }
                    $data[$key]['partnership']['total'] = array_sum($dataNote);
                }
            }
        }
        return $data;
    }
}
