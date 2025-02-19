<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesController extends AbstractController
{
    #[Route('/services/{id}', name: 'services')]
    public function servicesPage($id)
    {
        return $this->render('services/services.html.twig',[
            'id'=>$id
        ]);
    }
}
