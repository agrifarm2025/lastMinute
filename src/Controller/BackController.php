<?php

namespace App\Controller;

use App\Repository\FarmRepository;
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
    
    

}
