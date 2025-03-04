<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



final class FarmController extends AbstractController
{
    #[Route('/farm', name: 'farm')]
    public function farm(ManagerRegistry $m,Request $req): Response
    {  
        $em = $m->getManager();  
        $farm = new Farm();  
        $form=$this->createForm(FarmType::class,$farm);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $em->persist($farm);  
        $em->flush();  
        return $this->redirectToRoute('farmtab');
        }
        return $this->render("front/farm/farmcreate.html.twig",[
            'form'=>$form
        ]);  
    }  
    #[Route('/farmtab', name: 'farmtab')]
    public function farmdisplay(FarmRepository $farm)
    {
        $farmdb=$farm->findall();
        return $this->render("front/farm/farmtab.html.twig", [
            "tab"=> $farmdb
            
        ]);


    }
    #[Route('/list/{id}', name: 'list')]  
    public function show_books(FarmRepository $farms,$id)  
{  
    $farm=$farms->find($id);
    $fields=$farms->getFieldJoin($farm);
    
    return $this->render('front/field/fieldtab.html.twig', [  
        'farm'=>$farm ,'fields'=>$fields
    ]);  
}  
#[Route('/deletefarm/{id}', name: 'delete_farm')]  
    public function deletefarm(FarmRepository $rep,ManagerRegistry $m,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $farm=$rep->find($id);
        
        $em->remove($farm);  
        $em->flush();  
        return $this->redirectToRoute('farmtab');        }
        
    #[Route('/farmupdate/{id}', name: 'farm_update')]
    public function updatefield(ManagerRegistry $m,Request $req,FarmRepository $rep ,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $farm=$rep->find($id); 
        $form=$this->createForm(FarmType::class,$farm);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $em->persist($farm);  
        $em->flush();  
        return $this->redirectToRoute('farmtab');
        }
        return $this->render("front/farm/farmupdate.html.twig",[
            'form'=>$form
        ]);  
    } 

    #[Route('/farm/{id}/qr', name: 'farm_qr', methods: ['GET'])]
    public function generateQrCode(Farm $farm, SessionInterface $session): JsonResponse
    {
        // Generate a new random 6-digit access code
        $randomCode = (string) random_int(100000, 999999);

        // Store the access code in session
        $session->set('farm_access_code_' . $farm->getId(), $randomCode);

        // Generate the QR Code containing the access code
        $qrData = sprintf("Access Code: %s", $randomCode);
        $qrCode = Builder::create()
            ->writer(new SvgWriter())
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->build();

        // Return QR Code and access code in JSON response
        return new JsonResponse([
            'qrCode' => $qrCode->getDataUri(),
            'accessCode' => $randomCode,
        ]);
    }

    #[Route('/farm/verify/{id}', name: 'verify_farm_access', methods: ['POST'])]
    public function verifyAccess(int $id, Request $request, SessionInterface $session): JsonResponse
    {
        $enteredCode = $request->request->get('access_code');
        $generatedCode = $session->get('farm_access_code_' . $id);

        if ($enteredCode === $generatedCode) {
            return new JsonResponse(['success' => true, 'redirectUrl' => $this->generateUrl('list', ['id' => $id])]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Invalid access code.']);
        }
    }
}

    

