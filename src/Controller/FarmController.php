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
    
}
