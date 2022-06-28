<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Mail;
use App\Entity\Price;
use App\Entity\Profile;
use App\Entity\PublicityCompany;
use App\Entity\PublicityFiles;
use App\Event\EmailEvent;
use App\Form\PublicityCompanyType;
use App\Helper\ExtensionFiles;
use App\Repository\PriceRepository;
use App\Repository\PublicityCompanyRepository;
use App\Service\FileUploader;
use App\Service\PersistService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @package App\Controller
 * @Route("/api", name="publicity_company_api")
 */
class PublicityCompanyController extends MainController
{
    /**
     * @Route("/publicityCompany", name="get_all_publicityCompany", methods={"GET"})
     */
    public function index()
    {
        $publicityCompany = $this->em->getRepository(PublicityCompany::class)->findAll();
        $prices = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        $data = $this->getData($publicityCompany, $prices);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/publicityCompany/{id}", name="get_one_publicityCompanies", methods={"GET"})
     */
    public function show(int $id)
    {
        $publicityCompany = $this->em->getRepository(PublicityCompany::class)->findBy(['id' => $id]);
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        $data = $this->getData($publicityCompany, $price);
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/myPurchases/{id}", name="get_myPurchases", methods={"GET"})
     */
    public function mesAchats(int $id)
    {
        $publicityCompany = $this->em->getRepository(PublicityCompany::class)->findAllPublicityByCompany($id);
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        $data = [];
        foreach ($publicityCompany as $pubfiles){
            foreach ($pubfiles->getPublicityFiles() as $value){
                    $data[] = [
                        'id' => $pubfiles->getId(),
                        'user' => [ "id" => $pubfiles->getUser()->getId(),"email" => $pubfiles->getUser()->getEmail(),"username" => $pubfiles->getUser()->getUsername()],
                        'period' => $pubfiles->getPeriod(),
                        'price' => $price->getUnitPrice(),
                        'priceHT' => $pubfiles->getPriceHT(),
                        'priceTTC' => $pubfiles->getPriceTTC(),
                        'dateCreate' => $pubfiles->getCreatedAt()->format('Y-m-d'),
                        'status' => $pubfiles->getStatus(),
                        'priceDiscount' => $pubfiles->getPriceDiscount(),
                        'paymentReference' => $pubfiles->getPaymentReference(),
                        'profile' => $value->getProfile()->getId(),
                        'links' => $value->getLinks(),
                        'image' => $value->getImage(),
                        'pdf' => $value->getPdf(),
                    ];
            }
        }
        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/myPurchases/download/{id}", name="myPurchases_download")
     */
    public function myPurchasesDownload($id)
    {
        // On définit les options du PDF
        $pdfOptions = new Options();
        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);
        $publicityCompany = $this->em->getRepository(PublicityCompany::class)->find($id);
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        $data = [];
        foreach ($publicityCompany->getCompanies() as $value){
            $data[] = [
                'id' => $value->getId(),
                'nameCompany' => $value->getName(),
                'Adresse' => $value->getAddress(),
                'country' => $value->getCountry(),
                'price' => $price->getUnitPrice(),
                'priceHT' => $publicityCompany->getPriceHT(),
                'priceTTC' => $publicityCompany->getPriceTTC(),
            ];
        }
        // On génère le html
        $html = $this->renderView('publicity_company/index.html.twig',[
            'data' => $data
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // On génère un nom de fichier
        $fichier = 'facture-'. $publicityCompany->getCreatedAt()->format('Y-m-d') .'.pdf';
        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);
        return $this->successResponse(["code" => 200, "message" => "The PDF file has been succesfully generated !" ]);
    }

    /**
     * @Route("/getDevis/{id}", name="get_devis", methods={"GET"})
     */
    public function getDevis(int $id)
    {
        $publicityCompany = $this->em->getRepository(PublicityCompany::class)->find($id);
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        $data = [];
        $period = $publicityCompany->getPeriod();
        $date =$publicityCompany->getCreatedAt()->format('Y-m-d');
        $endDate = date('Y-m-d', strtotime($date. ' + '.$period.' days'));
        foreach ($publicityCompany->getCompanies() as $value){
            $data[] = [
                'id' => $value->getId(),
                'nameCompany' => $value->getName(),
                'price' => $price->getUnitPrice(),
                'priceHT' => $publicityCompany->getPriceHT(),
                'priceTTC' => $publicityCompany->getPriceTTC(),
                'startDate' => $publicityCompany->getCreatedAt()->format('Y-m-d'),
                'endDate' => $endDate,
                ];
        }

        if(isset($data)) {
            return $this->successResponse($data);
        }
    }

    /**
     * @Route("/publicityCompany", name="create_publicityCompany", methods={"POST"})
     */
    public function create(Request $request, PersistService $persistService, FileUploader $fileUploader,ExtensionFiles $extensionFiles,EventDispatcherInterface $dispatcher)
    {
        $data = $this->jsonDecode($request);
        $publicityCompany = new PublicityCompany();
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        try {
            $persistService->insert($request, PublicityCompanyType::class, $publicityCompany, $data);
            $company = $this->em->getRepository(Company::class)->find($data['company_id']);
            try {
                $files = $fileUploader->upload($request);
                if (isset($data['publicityFiles'])) {
                    $publicityFile = $data['publicityFiles'];
                    foreach ($publicityFile as $value) {
                        $publicityFiles = new PublicityFiles();
                        $publicityFiles->setLinks($value['links']);
                        $publicityFiles->setCompany($company);
                        $publicityFiles->setPublicityCompany($publicityCompany);
                        $profile = $this->em->getRepository(Profile::class)->find($value['profile_id']);
                        $publicityFiles->setProfile($profile);
                        if ($files != null) {
                            foreach ($files as $file) {
                                $image = $fileUploader->ImageUploade(array($file), $this->em);
                                $extensionFiles->extFiles($image,$publicityFiles);
                            }
                        }
                        $this->em->persist($publicityFiles);
                    }
                }
            } catch (\Exception $e) {
                return $this->successResponse(["code" => 409, "message" => "you have problem on calling Publicity or in upload files"], 409);
            }
            $publicityCompany->addCompanies($company);
            if($company->getType() === 'siège') {
                $publicityCompany->setPriceHT($price->getSeatPrice() * $publicityCompany->getPeriod());
            } else {
                $publicityCompany->setPriceHT($price->getUnitPrice() * $publicityCompany->getPeriod());
            }
            $this->em->persist($publicityCompany);
            $publicityCompany->setPriceTTC($publicityCompany->getPriceHT() + ($publicityCompany->getPriceHT() * $price->getTva() / 100));
            if($publicityCompany->getPeriod() <= 31) {
                $publicityCompany->setPriceDiscount($publicityCompany->getPeriod() * ($price->getDiscountMonth() / 100));
            } elseif ($publicityCompany->getPeriod() > 31 && $publicityCompany->getPeriod() <= 93) {
                $publicityCompany->setPriceDiscount($publicityCompany->getPeriod() * ($price->getDiscount3Month() / 100));
            } elseif ($publicityCompany->getPeriod() > 93) {
                $publicityCompany->setPriceDiscount($publicityCompany->getPeriod() * ($price->getDiscount6Month() / 100));
            }
            $publicityCompany->addPublicityFiles($publicityFiles);
            $publicityCompany->setStatus(false);
            $publicityCompany->setUser($this->getUser());
            $this->em->persist($publicityCompany);
            $this->em->flush();
            $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'Achat_espace_publicitaire'));
            $dispatcher->dispatch(new EmailEvent($mail,$this->getUser(),null),EmailEvent::PUBLICITY);
            return $this->successResponse(["code" => 200, "message" => "publicityCompany successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()], 409);
        }
    }

    /**
     * @Route("/publicityCompany/{id}", name="edit_publicityCompany", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService,FileUploader $fileUploader,ExtensionFiles $extensionFiles)
    {
        $data = $this->jsonDecode($request);
        $publicityComapny = $this->em->getRepository(PublicityCompany::class)->findOneBy(['id' => $id]);
        $publicityFiles = $this->em->getRepository(PublicityFiles::class)->findBy(['publicityCompany' => $publicityComapny->getId()]);
        try {
            $persistService->update($request,PublicityCompanyType::class,$publicityComapny,$data);
            try {
                $files = $fileUploader->upload($request);
                if (isset($data['publicityFiles'])) {
                    $publicityFile = $data['publicityFiles'];
                    foreach ($publicityFile as $values) {
                        foreach ($publicityFiles as $value) {
                            $profile = $this->em->getRepository(Profile::class)->find($values['profile_id']);
                            if($profile == $value->getProfile()){
                                $value->setLinks($values['links']);
                            }
                            if ($files != null) {
                                foreach ($files as $file) {
                                    $image = $fileUploader->ImageUploade(array($file), $this->em);
                                    $oldPdfFile = $value->getPdf();
                                    $oldFile = $value->getImage();
                                    $extensionFiles->extFiles($image,$publicityFiles);
                                    $this->em->remove($oldPdfFile);
                                    $this->em->remove($oldFile);
                                }
                            }
                            $this->em->persist($value);
                        }
                    }

            }
            } catch (\Exception $e) {
                return $this->successResponse(["code" => 409, "message" => "you have problem on calling Publicity or in upload files"], 409);
            }
            $this->em->persist($publicityComapny);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "publicityCompany successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/publicityCompany", name="delte_publicityCompany", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $publicity = $this->em->getRepository(PublicityCompany::class)->find($id);
            $this->em->remove($publicity);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "publicityCompany successfully deleted"]);
    }

    /**
     * @param array $publicityCompany
     * @param $prices
     * @return array
     */
    public function getData(array $publicityCompany, $prices): array
    {
        $data = [];
        foreach ($publicityCompany as $key => $value) {
            $data[] = [
                'id' => $value->getId(),
                'user' => ["id" => $value->getUser()->getId(), "email" => $value->getUser()->getEmail(), "username" => $value->getUser()->getUsername()],
                'period' => $value->getPeriod(),
                'price' => $prices->getUnitPrice(),
                'priceHT' => $value->getPriceHT(),
                'priceTTC' => $value->getPriceTTC(),
                'dateCreate' => $value->getCreatedAt()->format('Y-m-d'),
                'status' => $value->getStatus(),
                'priceDiscount' => $value->getPriceDiscount(),
                'paymentReference' => $value->getPaymentReference(),
            ];
            foreach ($value->getCompanies() as $company) {
                $data[$key]['company'] = [
                    'id' => $company->getId(),
                    'name' => $company->getName(),
                ];
            }
        }
        return $data;
    }
}
