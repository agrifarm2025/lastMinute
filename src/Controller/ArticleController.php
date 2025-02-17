<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_show')]
    public function show(): Response
    {
        return $this->render('article/articledetail.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
}
