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
        $field=$fields->find($id);
        $crop=$field->getCrop()->getId();
        return $this->render('services/services.html.twig',[
            'id'=>$id,
            'crop'=>$crop
        ]);
    }
}
