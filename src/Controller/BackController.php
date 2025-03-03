<?php

namespace App\Controller;

use App\Repository\FarmRepository;
use App\Repository\FieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackController extends AbstractController
{
    #[Route('/back', name: 'back')]
    public function tab(FarmRepository $farm)

    {
        $farmdb=$farm->findall();
        return $this->render('back/task-back.html.twig',[
            "farm"=> $farmdb
            
        ]);
    }
    #[Route('/join/{id}', name: 'join')]
    public function ta(FarmRepository $farms,$id)

    {
        $farm=$farms->find($id);
        $fields=$farms->getFieldJoin($farm);
        
        return $this->render('back/field.html.twig', [  
            'farm'=>$farm ,'fields'=>$fields
        ]);  
    }
    #[Route('/joinf/{id}', name: 'joinf')]  
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
    
        return $this->render('back/task.html.twig', [
            'id' => $field->getFarm()->getId(),
            'field' => $field,
            'todo' => $todo,
            'inprogress' => $inprogress,
            'done' => $done
        ]);
    }
    
    

}

