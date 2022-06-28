<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\Price;
use App\Entity\Publicity;
use App\Event\EmailEvent;
use App\Form\PublicityType;
use App\Service\FileUploader;
use App\Service\PersistService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @package App\Controller
 * @Route("/api", name="publicity_api")
 */
class PublicityController extends MainController
{
    /**
     * @Route("/publicities", name="get_all_publicities", methods={"GET"})
     */
    public function index()
    {
        $publicities = $this->em->getRepository(Publicity::class)->findAll();
        if(isset($publicities)) {
            return $this->successResponse($publicities);
        }
    }

    /**
     * @Route("/publicities/{id}", name="get_one_publicity", methods={"GET"})
     */
    public function show($id)
    {
        $publicity = $this->em->getRepository(Publicity::class)->find($id);
        if(isset($publicity)) {
            return $this->successResponse($publicity);
        }
    }
    /**
     * @Route("/getPurchasesByUser/{id}", name="get_myPurchases_publicity", methods={"GET"})
     */
    public function mesAchatsPublicity(int $id)
    {
        $publicityPurchases = $this->em->getRepository(Publicity::class)->findAllPublicityByUser($id);
        if(isset($publicityPurchases)) {
            return $this->successResponse($publicityPurchases);
        }
    }
    /**
     * @Route("/myPurchasesPublicity/download/{id}", name="myPurchases_publicity_download")
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
        $publicity = $this->em->getRepository(Publicity::class)->find($id);
            $data[] = [
                'id' => $publicity->getId(),
                'startDate' => $publicity->getStartDay(),
                'endDate' => $publicity->getEndDay(),
                'price' => $publicity->getPrice(),
            ];
        // On génère le html
        $html = $this->renderView('publicity/index.html.twig',[
            'data' => $data
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // On génère un nom de fichier
        $fichier = 'facture-'. $publicity->getStartDay()->format('d-m-Y') .'.pdf';
        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);
        return $this->successResponse(["code" => 200, "message" => "The PDF file has been succesfully generated !" ]);
    }

    /**
     * @Route("/publicities", name="create_publicity", methods={"POST"})
     */
    public function create(Request $request,PersistService $persistService,FileUploader $fileUploader,EventDispatcherInterface $dispatcher)
    {
        $data = $this->jsonDecode($request);
        $publicity = new Publicity();
        $price = $this->em->getRepository(Price::class)->findOneBy(['isActive' => true]);
        try {
            $persistService->insert($request,PublicityType::class,$publicity,$data);
            $dateStart = new \DateTime($data['startDay']);
            $dateEnd = new \DateTime($data['endDay']);
            $interval = $dateStart->diff($dateEnd)->days / 7;
            $publicity->setPrice($price->getWeekPricePublicitySearch()*(int)$interval);
            $publicity->setStatus(false);
            $publicity->setUser($this->getUser());
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $publicity->setImage($image);
                }
                if(isset($file['pdf'])){
                    $photo = $fileUploader->ImageUploade(array($file['pdf']), $this->em);
                    $publicity->setPdf($photo);
                }
            }
            $this->em->persist($publicity);
            $this->em->flush();
            $mail = $this->em->getRepository(Mail::class)->findOneBy(array('name' => 'admin_new_search_page_publicity'));
            $dispatcher->dispatch(new EmailEvent($mail,$this->getUser(),$publicity->getId()),EmailEvent::NEWPUBLICITYSEARCH);
            return $this->successResponse(["code" => 200, "message" => "publicity successfully added"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/publicities/{id}", name="edit_publicity", methods={"POST"})
     */
    public function update(Request $request,$id,PersistService $persistService,FileUploader $fileUploader)
    {
        $data = $this->jsonDecode($request);
        $publicity = $this->em->getRepository(Publicity::class)->findOneBy(['id' => $id]);
        try {
            $persistService->update($request,PublicityType::class,$publicity,$data);
            $file = $fileUploader->upload($request);
            if($file != null){
                if(isset($file['image'])){
                    $image = $fileUploader->ImageUploade(array($file['image']), $this->em);
                    $publicity->setImage($image);
                }
                if(isset($file['pdf'])){
                    $photo = $fileUploader->ImageUploade(array($file['pdf']), $this->em);
                    $publicity->setPdf($photo);
                }
            }
            $this->em->persist($publicity);
            $this->em->flush();
            return $this->successResponse(["code" => 200, "message" => "publicity successfully edited"]);
        } catch (NotEncodableValueException $e) {
            return $this->successResponse(["code" => 409, "message" => $e->getMessage()],409);
        }
    }

    /**
     * @Route("/publicities", name="delte_publicity", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        $data = $this->jsonDecode($request);
        foreach ($data['ids'] as $id) {
            $publicity = $this->em->getRepository(Publicity::class)->find($id);
            $this->em->remove($publicity);
        }
        $this->em->flush();
        return $this->successResponse(["code" => 200, "message" => "publicity successfully deleted"]);
    }
}
