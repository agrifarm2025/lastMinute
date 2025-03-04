<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChartController extends AbstractController
{
    #[Route('/chart', name: 'app_chart')]
    public function index(): Response
    {
        return $this->render('chart/index.html.twig');
    }
}
