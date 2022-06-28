<?php

namespace App\Controller;

use App\Entity\Naf;
use App\Entity\Sector;
use App\Form\SectorType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="sector_api")
 */
class SectorController extends MainController
{
    /**
     * @Route("/sectors", name="get_all_sectors" , methods={"GET"})
     */
    public function index()
    {
        $sectors = $this->em->getRepository(Sector::class)->findAll();
        if(isset($sectors)) {
            return $this->successResponse($sectors);
        }
    }
    /**
     * @Route("/sectors/{id}", name="get_one_sector" , methods={"GET"})
     */
    public function show($id)
    {
        $sector = $this->em->getRepository(Sector::class)->find($id);
        if(isset($sector)) {
            return $this->successResponse($sector);
        }
    }
    /**
     * @Route("/sectors", name="create_sectors", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService,FileUploader $fileUploader)
    {
        $data = $this->jsonDecode($request);
        $sector = new Sector();
        try {
            $persistService->insert($request,SectorType::class,$sector,$data);
            if(isset($data['parent'])) {
                $parent = $this->em->getRepository(Sector::class)->find($data['parent']);
                $sector->setParent($parent);
            }
            if(isset($data['nafs'])) {
                $nafs = $data['nafs'];
                foreach ($nafs as $value) {
                    $naf = $this->em->getRepository(Naf::class)->find($value);
                    $sector->addNafs($naf);
                }
            }
            $file = $fileUploader->upload($request);
            if($file != null){
                $result = $fileUploader->ImageUploade($file, $this->em);
                $sector->setImage($result);
            }
            $this->em->persist($sector);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "sector successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @param array $nafs
     * @param Sector $param
     */
    private function removeOldNaf(array $nafs,Sector $param)
    {
        foreach ($nafs as $naf) {
            $oldnaf = $this->em->getRepository(Naf::class)->find($naf['id']);
            $param->removeNafs($oldnaf);
            $this->em->persist($param);
            $this->em->flush();
        }
    }

    /**
     * @Route("/sectors/{id}", name="edit_sectors", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService,FileUploader $fileUploader)
    {
        $data = $this->jsonDecode($request);
        $sector = $this->em->getRepository(Sector::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,SectorType::class,$sector,$data);
            if(isset($data['parent'])) {
                $parent = $this->em->getRepository(Sector::class)->find($data['parent']);
                $sector->setParent($parent);
            }
            if(isset($data['nafs'])) {
                $nafBySector = $this->em->getRepository(Sector::class)->findNafBySector($id);
                $this->removeOldNaf($nafBySector,$sector);
                $nafs = $data['nafs'];
                foreach ($nafs as $value) {
                    $naf = $this->em->getRepository(Naf::class)->find($value);
                    $sector->addNafs($naf);
                }
            }
            $file = $fileUploader->upload($request);
            if($file != null){
                $result = $fileUploader->ImageUploade($file, $this->em);
                $sector->setImage($result);
            }
            $this->em->persist($sector);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "sector successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }
    /**
     * @Route("/sectors", name="delete_sectors", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $sector = $this->em->getRepository(Sector::class)->find($id);
            $this->em->remove($sector);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "sector successfully deleted"]);
    }
}
