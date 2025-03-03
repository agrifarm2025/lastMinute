<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function front(): Response
    {
        return $this->render('front/base-front.html.twig');
    }
   
    
}
