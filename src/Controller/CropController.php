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
use App\Entity\Farm;
use App\Entity\Field;
use App\Entity\Soildata;
use App\Form\SoildataType;
use App\Repository\SoildataRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;

class CropController extends AbstractController
{
    #[Route('/crop/add/{id}', name: 'app_crop_add', methods: ['GET', 'POST'])]
public function add(Request $request, EntityManagerInterface $entityManager, $id): Response
{
    $crop = new Crop();
    $form = $this->createForm(CropType::class, $crop);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // 🔹 Récupérer la température depuis le capteur
        $temperature = $this->getTemperatureFromSensor();

        if ($temperature !== null) {
            $crop->setTemperature($temperature);
        }

        // 🔹 Sauvegarde du crop
        $entityManager->persist($crop);
        $entityManager->flush();

        $this->addFlash('success', 'Crop ajouté avec succès avec température: ' . $temperature . '°C !');

        return $this->redirectToRoute('field', ['id' => $id]);
    }

    return $this->render('crop/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

/**
 * 🔥 Fonction pour récupérer la température depuis l'ESP8266
 */
private function getTemperatureFromSensor(): ?float
{
    $espServer = "http://192.168.70.1/projet/crop/temperature";

    try {
        $response = file_get_contents($espServer);
        if ($response === false) {
            throw new \Exception("Impossible d'obtenir la température.");
        }

        return floatval($response);
    } catch (\Exception $e) {
        return null; // En cas d'erreur, on met une température nulle
    }
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

        return $this->redirectToRoute('affichage');
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

    
    #[Route("/crop/affichage/{id}", name:"crop_affichage")]
    public function affichage(CropRepository $em, $id): Response
    {
        $crops = $em->find($id);
        return $this->render('crop/affichage.html.twig', [
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
#[Route('/listcrop/{id}', name: 'listcrop')]  
public function show_books(CropRepository $farms,$id)  
{  
$farm=$farms->find($id);
$soil=$farms->getCropJoin($farm);

return $this->render('soildata/affichage_soil.html.twig', [ 
    'soil' => $soil,'crop'=>$farm
]);  

}
#[Route('/crop', name: 'app_crop')]
public function indexe(EntityManagerInterface $entityManager): Response
{
    $crops = $entityManager->getRepository(Crop::class)->findAll();

    return $this->render('crop/backcropaffiche.html.twig', [
        'crops' => $crops,
    ]);
}
#[Route('/soildata', name: 'app_soil')]
public function index(EntityManagerInterface $entityManager): Response
{
    $soil = $entityManager->getRepository(Soildata::class)->findAll();

    return $this->render('Crop/backsoilaffiche.html.twig', [
        'soil' => $soil,
    ]);
}





}