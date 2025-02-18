<?php

// src/Controller/CropgrowthController.php

namespace App\Controller;

use App\Entity\Cropgrowth;
use App\Entity\Stage;
use App\Entity\Stages;
use App\Form\CropgrowthType;
use App\Form\StageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CropgrowthController extends AbstractController
{
    #[Route('/cropgrowth/add', name: 'app_cropgrowth_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cropGrowth = new Cropgrowth();
    
        // Predefine and persist the 4 stages
        $stages = ['Plantation', 'Croissance', 'Maturité', 'Récolte'];
        foreach ($stages as $stageName) {
            $stage = new Stages($stageName);
            $entityManager->persist($stage); // Persist the stage
            $cropGrowth->addStage($stage);
        }
    
        // Create the form
        $form = $this->createForm(CropgrowthType::class, $cropGrowth);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cropGrowth);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_cropgrowth_add');
        }
    
        return $this->render('cropgrowth/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

