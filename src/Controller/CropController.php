<?php
namespace App\Controller;

use App\Entity\Crop;
use App\Form\CropType;
use App\Repository\CropRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ManagerRegistry;
use App\Form\SoildataType;
use App\Repository\SoildataRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;

class CropController extends AbstractController
{
    #[Route('/crop/add', name: 'app_crop_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $crop = new Crop();
        $form = $this->createForm(CropType::class, $crop);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crop);
            $entityManager->flush();
    
            $this->addFlash('success', 'Crop ajouté avec succès !');
    
            return $this->redirectToRoute('app_soil_add', ['cropId' => $crop->getId()]);
        }
    
        return $this->render('crop/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/updatecrop/{id}', name: 'app_updateformcrop', methods: ['GET', 'POST'])]
public function updateformcrop(Request $request, EntityManagerInterface $entityManager, CropRepository $rep, int $id): Response
{
    $crop = $rep->find($id);

    if (!$crop) {
        throw $this->createNotFoundException("Crop not found");
    }

    $form = $this->createForm(CropType::class, $crop);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($crop);
        $entityManager->flush();

        $this->addFlash('success', 'Crop updated successfully!');

        return $this->redirectToRoute('crop_affichage');
    }

    return $this->render('crop/add.html.twig', [
        'form' => $form->createView(), 
    ]);
}

    #[Route("/crop", name: "app_crop")] 
    public function marketplace(EntityManagerInterface $em): Response
    {
        $crops = $em->getRepository(Crop::class)->findAll();

        return $this->render('crop/khraa.html.twig', [ 
            'crops' => $crops, 
        ]);
    }

    
    #[Route("/crop/affichage", name:"crop_affichage")]
    public function affichage(CropRepository $em): Response
{
    $crops = $em->findAll();
    return $this->render('crop/affichage.html.twig', [ // Fixed template name
        'products' => $crops,
    ]);
    
}

#[Route('/deletecrop/{id}', name: 'delete_crop')]  
public function deleteCrop(CropRepository $cropRepository, EntityManagerInterface $em, $id): Response 
{  
    $crop = $cropRepository->find($id);

    if (!$crop) { 
        $this->addFlash('error', 'Crop not found.');
        return $this->redirectToRoute('crop_affichage');
    }

    $soildataList = $crop->getSoildata();

    foreach ($soildataList as $soildata) {
        $em->remove($soildata);
    }
    
    $em->remove($crop);  
    $em->flush();  

    $this->addFlash('success', 'Crop and related soil data deleted successfully.');
    return $this->redirectToRoute('crop_affichage');
}
#[Route('/list/{id}', name: 'list')]  
public function show_books(CropRepository $farms,$id)  
{  
$farm=$farms->find($id);
$soil=$farms->getCropJoin($farm);

return $this->render('soildata/affichage_soil.html.twig', [ 
    'soil' => $soil,'crop'=>$farm
]);  

}
}