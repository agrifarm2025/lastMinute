<?php

namespace App\Controller;
use App\Entity\Crop; 

use App\Entity\Soildata;
use App\Repository\SoildataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Form\SoildataType;





final class SoildataController extends AbstractController
{

    #[Route("/soildata/affichage_soil", name:"soildata_affichage")]
    public function affichage(SoildataRepository $em): Response
{
    $soil = $em->findAll();
    return $this->render('soildata/affichage_soil.html.twig', [ 
        'soil' => $soil,
    ]);
    
}




#[Route('/soildata/addsoil/{cropId}', name: 'app_soil_add')]
public function addsoil(Request $request, EntityManagerInterface $em, int $cropId)
{
    $crop = $em->getRepository(Crop::class)->find($cropId);
    if (!$crop) {
        throw $this->createNotFoundException("Crop not found");
    }

    $soil = new Soildata();
    $soil->setCrop($crop); 
    
    $form = $this->createForm(SoildataType::class, $soil);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($soil);
        $em->flush();

        $this->addFlash('success', 'Soil ajouté avec succès !');

        return $this->redirectToRoute('crop_affichage'); 
    }

    return $this->render('soildata/addsoil.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/updatesoil/{id}', name: 'app_updateformsoil')]
    public function updateformsoil(Request $request, EntityManagerInterface $em, SoildataRepository $rep, int $id): Response
    {
        $soil = $rep->find($id);
        
        if (!$soil) {
            throw $this->createNotFoundException("Soil data not found");
        }
    
        $form = $this->createForm(SoildataType::class, $soil);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Ensure any needed changes to the entity
            // Remove setEnable(true) if not in your entity
            $em->persist($soil);
            $em->flush();
    
            $this->addFlash('success', 'Soil data updated successfully!');
    
            return $this->redirectToRoute('crop_affichage');
        }
    
        return $this->render('soildata/addsoil.html.twig', [
            'form' => $form->createView(), 
        ]);
    }

        #[Route('/deletesoil/{id}', name: 'delete_soil')]  
        public function deleteSoil(SoildataRepository $soildataRepository, EntityManagerInterface $em, $id): Response 
        {  
            $soil = $soildataRepository->find($id);
        
            if (!$soil) { 
                $this->addFlash('error', 'Soil data not found.');
                return $this->redirectToRoute('crop_affichage');
            }
        
            $em->remove($soil);  
            $em->flush($soil);  
        
            $this->addFlash('success', 'Soil data deleted successfully.');
            return $this->redirectToRoute('soildata_affichage');
        }
        
}
