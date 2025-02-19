<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Entity\Field;
use App\Form\FieldType;
use App\Repository\FieldRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FieldController extends AbstractController
{
   
    #[Route('/field/{id}', name: 'field')]
    public function form(ManagerRegistry $m,Request $req,$id ) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $field = new Field(); 
        $farm = $em->getRepository(Farm::class)->find($id);

        if (!$farm) {
            throw $this->createNotFoundException('Farm not found');
        }
    
        // Set the farm for the new Field entity
        $field->setFarm($farm);
        $form=$this->createForm(FieldType::class,$field);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $field->setIncome(0);
        $field->setOutcome(0);
        $field->setProfit(0);
        $em->persist($field);  
        $em->flush();  
        
        return $this->redirectToRoute('list', ['id' => $field->getFarm()->getId()]);
        }
        return $this->render("front/field/fieldcreate.html.twig",[
            'form'=>$form
        ]);  
    }
    #[Route('/deletefield/{id}', name: 'delete_field')]  
    public function deletefield(FieldRepository $rep,ManagerRegistry $m,Request $req,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $field=$rep->find($id);
        
        $em->remove($field);  
        $em->flush();  
        return $this->redirectToRoute('list', ['id' => $field->getFarm()->getId()]);
        } 
       
    
    #[Route('/fieldupdate/{id}', name: 'field_update')]
    public function updatefield(ManagerRegistry $m,Request $req,FieldRepository $rep ,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $field=$rep->find($id); 
        $form=$this->createForm(FieldType::class,$field);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $em->persist($field);  
        $em->flush();  
        return $this->redirectToRoute('list', ['id' => $field->getFarm()->getId()]);
        }
        return $this->render("front/field/fieldupdate.html.twig",[
            'form'=>$form
        ]);  
    }  
    #[Route('/task/{id}', name: 'task')]  
public function show_fields(FieldRepository $fieldRepository, $id): Response
{
    $field = $fieldRepository->find($id);
    if (!$field) {
        throw $this->createNotFoundException('No field found for id ' . $id);
    }

   
    foreach ($field as $fields) {
        $profit = $fields->getIncome() - $fields->getOutcome(); // Calculate profit
        $fields->setProfit($profit);  // Assuming you have a setter for profit in your Field entity
    }
    $todo = $fieldRepository->getToDo($field);
    $inprogress = $fieldRepository->getInProgress($field);
    $done = $fieldRepository->getDone($field);

    return $this->render('front/task/tasktab.html.twig', [
        'id' => $field->getFarm()->getId(),
        'field' => $field,
        'todo' => $todo,
        'inprogress' => $inprogress,
        'done' => $done
    ]);
}

#[Route('/services', name: 'app_services')]
public function servicesPage()
{

    return $this->render('services/services.html.twig');
}
}
