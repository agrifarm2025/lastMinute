<?php

namespace App\Controller;

use App\Repository\FieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesController extends AbstractController
{
    #[Route('/services/{id}', name: 'services')]
    public function servicesPage(FieldRepository $fields,$id)
    {
        $field = $fields->find($id);
        

        
        $crop=$field->getCrop();
        if ($crop === null || $crop->getId() === null) {
            $todo = $fields->getToDo($field);
        $inprogress = $fields->getInProgress($field);
        $done = $fields->getDone($field);

        return $this->render('front/task/tasktab.html.twig', [
            'farm'=>$field->getFarm(),
            'id'=>$field->getFarm()->getId(),
            'field' => $field,
            'todo' => $todo,
            'inprogress' => $inprogress,
            'done' => $done ]);
        }
        else{
            $crop=$field->getCrop()->getId();
            return $this->render('services/services.html.twig',[
                'farm'=>$field->getFarm(),
            'id'=>$field->getFarm()->getId(),
            'field' => $field,
            'crop'=>$crop
            ]);
        }
       
    }
}
