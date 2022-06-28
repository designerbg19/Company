<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Company;
use App\Entity\Files;
use App\Entity\LegalStatus;
use App\Entity\Macaron;
use App\Entity\Mail;
use App\Entity\Note;
use App\Entity\Sector;
use App\Entity\Tag;
use App\Event\EmailEvent;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @param CompanyRepository $companyRepository
 * @package App\Controller
 * @Route("/api", name="company_api")
 */
class CompanyController extends MainController
{
    /**
     * @Route("/companies", name="get_all_companies", methods={"GET"})
     */
    public function getAllCompanies()
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        $data = $this->getData($companies);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/cities", name="get_all_cities", methods={"GET"})
     */
    public function getAllCities()
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        $data = [];
        foreach ($companies as $value) {
            if (!empty($value->getCity())) {
                $data[] = [
                    'city' => $value->getCity(),
                ];
            }
        }
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/companies/{id}", name="get_one_company", methods={"GET"})
     */
    public function getById($id)
    {
        $company = $this->em->getRepository(Company::class)->findBy(['id'=>$id]);
        $data = $this->getData($company);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/findBySector/{id}", name="get_by_sector", methods={"GET"})
     */
    public function findBySector($id)
    {
        $companyBySector = $this->em->getRepository(Company::class)->findCompanyBySectors($id);
        if(isset($companyBySector)) {
            return $this->successResponse($companyBySector);
        }
    }

    /**
     * @Route("/findByTags", name="get_by_tags", methods={"POST"})
     */
    public function findByTags(Request $request)
    {
        //TODO::tag=>4,7
        $tag = $request->request->get('tag');
        $companyBySector = $this->em->getRepository(Company::class)->findCompanyByTags($tag);
        if(isset($companyBySector)) {
            return $this->successResponse($companyBySector);
        }
    }

    /**
     * @Route("/findByName", name="get_by_name", methods={"POST"})
     */
    public function findByName(Request $request)
    {
        $name = $request->request->get('name');
        $companyByName = $this->em->getRepository(Company::class)->findCompanyByName($name);
        if(isset($companyByName)) {
            return $this->successResponse($companyByName);
        }
    }
    /**
     * @Route("/companyFilter", name="company_filter", methods={"POST"})
     */
    public function findByAll(Request $request)
    {
        $name = $request->request->get('name');
        $tag = $request->request->get('tag');
        $sector = $request->request->get('sector');
        $place = $request->request->get('city');
        $companyfilter = $this->em->getRepository(Company::class)->findCompanyByFilter($name, $sector, $tag, $place);
        $data = $this->getData($companyfilter);
        if (count($data) > 0) {
            return $this->successResponse($data);
        } else {
            return $this->successResponse(["code" => 200, "message" => 'no data'],200);
        }
    }

    /**
     * @Route("/companiesFilters", name="company_plus_filter", methods={"POST"})
     */
    public function findByFilter(Request $request)
    {
        $country = $request->request->get('country');
        $macarons = $request->request->get('macarons');
        $note = $request->request->get('price');
        $society = $request->request->get('society');
        $environment = $request->request->get('environment');
        $companies = $this->em->getRepository(Company::class)->findAll();
        $scorePriceArray = $scoreSocietyArray = $scoreEnvironmentArray = [];
        foreach ($companies as $company) {
            $companyScore = $this->em->getRepository(Note::class)->findBy(['company' => $company->getId()]);
            $averagePriceScore = [];
            $averageSocietyScore = [];
            $averageEnvironmentScore = [];
            foreach ($companyScore as $score) {
                $averagePriceScore[] = $score->getNoteMoyennePrix();
                $averageSocietyScore[] = $score->getNoteMoyenneSociete();
                $averageEnvironmentScore[] = $score->getNoteMoyenneEnvironement();
            }
            $scorePriceArray[] = array('id' => $company->getId(), 'scorePrice' => array_sum($averagePriceScore));
            $scoreSocietyArray[] = array('id' => $company->getId(), 'scoreSociety' => array_sum($averageSocietyScore));
            $scoreEnvironmentArray[] = array('id' => $company->getId(), 'scoreEnvironment' => array_sum($averageEnvironmentScore));
        }
        if ($note !== null) {
            $resultPrice = $this->getClosest($note, null, null, $scorePriceArray);
        } else {
            $resultPrice = array();
        }
        if ($society !== null) {
            $resultSociety = $this->getClosest(null, $society, null, $scoreSocietyArray);
        } else {
            $resultSociety = array();
        }
        if ($environment !== null) {
            $resultEnvironment = $this->getClosest(null, null, $environment, $scoreEnvironmentArray);
        } else {
            $resultEnvironment = array();
        }
        $result = array_unique(array_merge($resultPrice, $resultSociety, $resultEnvironment));
        $companyFilter = $this->em->getRepository(Company::class)->findCompanyPlusFilter($country, $macarons, $result);
        $data = $this->getData($companyFilter);
        if (count($data) > 0) {
            return $this->successResponse($data);
        } else {
            return $this->successResponse(["code" => 200, "message" => 'no data'], 200);
        }
    }

    function getClosest($searchPrice, $searchSociety, $searchEnv, $arr)
    {
        $companySearch = [];
        foreach ($arr as $item) {
            if (isset($item['scorePrice']) && $item['scorePrice'] == $searchPrice) {
                $companySearch[] = $item;
            }
            if (isset($item['scoreSociety']) && $item['scoreSociety'] == $searchSociety) {
                $companySearch[] = $item;
            }
            if (isset($item['scoreEnvironment']) && $item['scoreEnvironment'] == $searchEnv) {
                $companySearch[] = $item;
            }
        }
        $companiesFilter = [];
        foreach ($companySearch as $company) {
            $companiesFilter[] = $company['id'];
        }
        return $companiesFilter;
    }

    /**
     * @Route("/companies", name="post_company", methods={"POST"})
     */
    public function insert(Request $request,FileUploader $fileUploader,PersistService $persistService,EventDispatcherInterface $dispatcher)
    {
        $data = $this->jsonDecode($request);
        $company = new Company();
        try {
           if(isset($data['calendar'])) {
               $horaires = $data['calendar'];
               foreach ($horaires as $value) {
                   $calendar = new Calendar();
                   $calendar->setDay($value['day']);
                   $calendar->setStart($value['start']);
                   $calendar->setEnd($value['end']);
                   $this->em->persist($calendar);
                   $company->addCalendar($calendar);
               }
           }
            $persistService->insert($request,CompanyType::class,$company,$data);
            if(isset($data['legalStatus_id'])) {
                $legalStatus = $this->em->getRepository(LegalStatus::class)->find($data['legalStatus_id']);
                $company->addLegalStatus($legalStatus);
            }
            if(isset($data['macarons'])) {
                $macarons = $data['macarons'];
                foreach ($macarons as $value) {
                    $macaron = $this->em->getRepository(Macaron::class)->find($value);
                    $company->addMacarons($macaron);
                }
            }
            if(isset($data['tags'])) {
                $tags = $data['tags'];
                foreach ($tags as $value) {
                    $tag = $this->em->getRepository(Tag::class)->find($value);
                    $company->addTags($tag);
                }
            }
            if(isset($data['sectors'])) {
                $sectors = $data['sectors'];
                foreach ($sectors as $value) {
                    $sector = $this->em->getRepository(Sector::class)->find($value);
                    $company->addSectors($sector);
                }
            }
            $files = $fileUploader->upload($request);
            if(isset($files['image'])){
                $result = $fileUploader->ImageUploade($files, $this->em);
                $company->setImage($result);
            }
            if(!isset($files['image'])){
                foreach ($files as $file) {
                    $result = $fileUploader->ImageUploade(array($file), $this->em);
                    $company->addGallery($result);
                }
            }
            $company->setUser($this->getUser());
            $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'administrateur_nouvelle_company'));
            $user = $this->getUser();
            $this->em->persist($company);
            $this->em->flush();
            $dispatcher->dispatch(new EmailEvent($mail,$user,$company->getName()),EmailEvent::COMPANY);
            return $this->successResponse(["code" => 200, "message" => 'company successfully added']);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/companies/{id}", name="update_company", methods={"PATCH","POST"})
     */
    public function update(Request $request,$id,FileUploader $fileUploader,PersistService $persistService)
    {
        $data = $this->jsonDecode($request);
        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $id]);
        $calendars = $this->em->getRepository(Company::class)->findCalendarByCompany($id);
        $oldStatus = $this->em->getRepository(Company::class)->findStatusByCompany($id);
        try {
            if(isset($data['calendar'])) {
                $horaires = $data['calendar'];
                foreach ($horaires as $value) {
                    foreach ($calendars as $calendar) {
                        $horaire = $this->em->getRepository(Calendar::class)->findOneBy(['id' => $calendar['id']]);
                        $horaire->setDay($value['day']);
                        $horaire->setStart($value['start']);
                        $horaire->setEnd($value['end']);
                        $this->em->persist($horaire);
                        $company->addCalendar($horaire);
                    }
                }
            }
            $persistService->update($request,CompanyType::class,$company,$data);
            if(isset($data['legalStatus_id'])) {
                $legalStatus = $this->em->getRepository(LegalStatus::class)->find($data['legalStatus_id']);
                if(!empty($oldStatus)){
                    $oldLegalStatus = $this->em->getRepository(LegalStatus::class)->find($oldStatus[0]['id']);
                    $company->removeLegalStatus($oldLegalStatus);
                }
                $company->addLegalStatus($legalStatus);
            }
            if(isset($data['macarons'])) {
                $macarons = $data['macarons'];
                foreach ($macarons as $value) {
                    $macaron = $this->em->getRepository(Macaron::class)->find($value);
                    $company->addMacarons($macaron);
                }
            }
            if(isset($data['tags'])) {
                $tags = $data['tags'];
                foreach ($tags as $value) {
                    $tag = $this->em->getRepository(Tag::class)->find($value);
                    $company->addTags($tag);
                }
            }
            if(isset($data['sectors'])) {
                $sectors = $data['sectors'];
                foreach ($sectors as $value) {
                    $sector = $this->em->getRepository(Sector::class)->find($value);
                    $company->addSectors($sector);
                }
            }
            $files = $fileUploader->upload($request);
            if(isset($files['image'])){
                $result = $fileUploader->ImageUploade($files, $this->em);
                $company->setImage($result);
            }
            if(!isset($files['image'])){
                foreach ($files as $file) {
                    $result = $fileUploader->ImageUploade(array($file), $this->em);
                    $company->addGallery($result);
                }
            }
            $this->em->persist($company);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => 'company successfully edited']);
        } catch (NotEncodableValueException $e){
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()]);
        }
    }

    /**
     * @Route("/companies", name="delte_all_company", methods={"DELETE"})
     * @param int $ids
     * @return Response|void
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $company = $this->em->getRepository(Company::class)->find($id);
            $this->em->remove($company);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => 'company successfully delete']);
    }

    /**
     * @param array $companies
     * @return array
     */
    public function getData(array $companies): array
    {
        $data = [];
        foreach ($companies as $value) {
            $data[] = [
                'id' => $value->getId(),
                'name' => $value->getName(),
                'email' => $value->getEmail(),
                'description' => $value->getDescription(),
                'socialReason' => $value->getSocialReason(),
                'address' => $value->getAddress(),
                'city' => $value->getCity(),
                'phone' => $value->getPhone(),
                'type' => $value->getType(),
                'companyType' => $value->getCompanyType(),
                'country' => $value->getCountry(),
                'siret' => $value->getSiret(),
                'siren' => $value->getSiren(),
                'tva' => $value->getTva(),
                'capital' => $value->getCapital(),
                'size' => $value->getSize(),
                'gelee' => $value->getGelee(),
                'managerEmail' => $value->getManagerEmail(),
                'managerCivility' => $value->getManagerCivility(),
                'managerLastname' => $value->getManagerLastname(),
                'managerFirstname' => $value->getManagerFirstname(),
                'addedBy' => $value->getAddedBy(),
                'siteAddress' => $value->getSiteAddress(),
                'postalCode' => $value->getPostalCode(),
                'createdAt' => $value->getCreatedAt(),
                'updatedAt' => $value->getUpdatedAt(),
                'image' => $value->getImage(),
                'calendar' => $value->getCalendar(),
            ];
        }
        foreach ($data as $key => $d) {
            if (!empty($d['id']) && $d['id'] != null) {
                $users = $this->em->getRepository(Company::class)->findUserByCompany($d['id']);
                if (count($users) > 0) {
                    foreach ($users as $user) {
                        $data[$key]['user'][] = [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'email' => $user['email'],
                        ];
                    }
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $macarons = $this->em->getRepository(Company::class)->findMacaronByCompany($d['id']);
                if (count($macarons) > 0) {
                    foreach ($macarons as $macaron) {
                        $data[$key]['macarons'][] = [
                            'name' => $macaron['name'],
                            'published' => $macaron['published'],
                        ];
                    }
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $legalstatus = $this->em->getRepository(Company::class)->findStatusByCompany($d['id']);
                if (count($legalstatus) > 0) {
                        $data[$key]['legalstatus'][] = [
                            'id' => $legalstatus[0]['id'],
                            'name' => $legalstatus[0]['name'],
                            'country' => $legalstatus[0]['country'],
                        ];
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $tags = $this->em->getRepository(Company::class)->findTagsByCompany($d['id']);
                if (count($tags) > 0) {
                    foreach ($tags as $tag) {
                        $data[$key]['tags'][] = [
                            'id' => $tag['id'],
                            'name' => $tag['name'],
                        ];
                    }
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $sectors = $this->em->getRepository(Company::class)->findSectorsByCompany($d['id']);
                if (count($sectors) > 0) {
                    foreach ($sectors as $sector) {
                        $data[$key]['sectors'][] = [
                            'id' => $sector['id'],
                            'name' => $sector['name'],
                        ];
                    }
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $notes = $this->em->getRepository(Note::class)->findNoteByCompany($d['id']);
                if (count($notes) > 0) {
                    $dataNote = [];
                    foreach ($notes as $note) {
                        array_push($dataNote, $note['score']);
                        $data[$key]['note'][] = [
                            'id' => $note['id'],
                            'score' => $note['score'],
                            'date' => $note['createdAt'],
                            'description' => $note['description'],
                            'profile' => ['id' => $note['profileId'], 'name' => $note['profile'], 'image' => $note['image']],
                            'user' => ['id' => $note['userId'], 'username' => $note['username'], 'email' => $note['email']]
                        ];
                    }
                    $data[$key]['total'] = array_sum($dataNote);
                }
            }
            if (!empty($d['id']) && $d['id'] != null) {
                $images = $this->em->getRepository(Files::class)->findBy(['company' => $d['id']]);
                if (count($images) > 0) {
                    foreach ($images as $image) {
                        $data[$key]['gallery'][] = [
                            'id' => $image->getId(),
                            'name' => $image->getName()
                        ];
                    }
                }
            }

        }
        return $data;
    }
}
