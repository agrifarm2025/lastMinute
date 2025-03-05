<?php

namespace App\Controller;

use App\Service\CropManageApiService;
use App\Entity\Soildata;
use App\Repository\SoildataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Form\SoildataType;
use App\Repository\CropRepository;
use App\Entity\Crop; 
use Symfony\Component\HttpFoundation\JsonResponse;



final class SoildataController extends AbstractController
{

    #[Route('/soildata/affichage_soil/{id}', name: 'soildata_affichage', methods: ['GET'])]
    public function affichageSoil(SoildataRepository $soildataRepository, CropRepository $cropRepository, int $id): Response
    {
        // Fetch the crop by the provided ID
        $crop = $cropRepository->find($id);
        
        if (!$crop) {
            throw $this->createNotFoundException('Crop not found.');
        }
    
        // Fetch all soil data associated with the crop
        $soil = $soildataRepository->findBy(['crop' => $crop]);
    
        return $this->render('soildata/affichage_soil.html.twig', [
            'soil' => $soil,
            'crop' => $crop, // Pass the crop variable to the template
            'debug_data' => dump($soil, $crop) // Debugging data

        ]);
    }
    




    #[Route('/soildata/addsoil/{cropId}', name: 'app_soil_add', defaults: ['cropId' => null])]
    public function addsoil(
        Request $request, 
        EntityManagerInterface $em, 
        CropManageApiService $cropManageApiService,
        CropRepository $cropRepository, // Added CropRepository to fetch the crop
        ?int $cropId
    ): Response {
        // ✅ Create new soil data object
        $soil = new Soildata();
    
        // ✅ Fetch the crop by ID
        if ($cropId !== null) {
            $crop = $cropRepository->find($cropId);
            if (!$crop) {
                throw $this->createNotFoundException('Crop not found.');
            }
            // ✅ Associate the soil data with the crop
            $soil->setCrop($crop);
        }
    
        // ✅ Fetch soil types from API
        $soilTypesFromApi = $cropManageApiService->getSoilTypes();
    
        // ✅ Convert API response to ChoiceType format
        $soilTypeChoices = [];
        foreach ($soilTypesFromApi as $soilType) {
            $soilTypeChoices[$soilType['Name']] = $soilType['Name'];
        }
    
        // ✅ Pass soil types to the form
        $form = $this->createForm(SoildataType::class, $soil, ['soil_types' => $soilTypeChoices]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Ensure the soil is linked to a crop before persisting
            if (!$soil->getCrop()) {
                $this->addFlash('error', 'No crop selected for the soil data.');
                return $this->redirectToRoute('crop_affichage');
            }
    
            $em->persist($soil);
            $em->flush();
    
            $this->addFlash('success', 'Soil ajouté avec succès !');
    
            // ✅ Redirect to the crop's soil data list
            return $this->redirectToRoute('soildata_affichage', ['id' => $soil->getCrop()->getId()]);
        }
    
        // ✅ Render the form and pass the crop ID
        return $this->render('soildata/addsoil.html.twig', [
            'form' => $form->createView(),
            'crop' => $crop ?? null, // Pass the crop if available
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
    public function deleteSoil(SoildataRepository $soildataRepository, EntityManagerInterface $em, int $id): Response 
    {  
        $soil = $soildataRepository->find($id);
    
        if (!$soil) { 
            $this->addFlash('error', 'Soil data not found.');
            return $this->redirectToRoute('crop_affichage'); // Ensure a valid fallback
        }
    
        // Store crop ID before deleting the soil data
        $cropId = $soil->getCrop()->getId(); 
    
        $em->remove($soil);  
        $em->flush();  
    
        $this->addFlash('success', 'Soil data deleted successfully.');
    
        // Redirect to the soil list page with the correct crop ID
        return $this->redirectToRoute('soildata_affichage', ['id' => $cropId]);
    }
    
        #[Route('/soildata', name: 'app_soil')]
public function index(EntityManagerInterface $entityManager): Response
{
    $soil = $entityManager->getRepository(Soildata::class)->findAll();

    return $this->render('soildata/backsoilaffiche.html.twig', [
        'soil' => $soil,
    ]);
}


    #[Route('/soil/statistics', name: 'app_soil_statistics')]
    public function indexx(SoildataRepository $soilRepository)
    {
        $soilData = $soilRepository->findAll();

        $phLevels = [];
        $humidityLevels = [];
        $nutrientLevels = [];
        $soilTypes = [];

        foreach ($soilData as $soil) {
            $phLevels[] = $soil->getNiveauPh();
            $humidityLevels[] = $soil->getHumidite();
            $nutrientLevels[] = $soil->getNiveauNutriment();
            $soilTypes[] = $soil->getTypeSol();
        }

        // Compter la fréquence de chaque type de sol
        $soilTypeCounts = array_count_values($soilTypes);

        return $this->render('soildata/statistics.html.twig', [
            'phLevels' => json_encode($phLevels),
            'humidityLevels' => json_encode($humidityLevels),
            'nutrientLevels' => json_encode($nutrientLevels),
            'soilTypeCounts' => json_encode($soilTypeCounts),
        ]);
    }
    #[Route('/crop/ideal-conditions', name: 'app_crop_ideal_conditions')]
    public function generateIdealConditions(SoildataRepository $soilRepository): Response
    {
        $soils = $soilRepository->findAll();
    
        if (empty($soils)) {
            $this->addFlash('error', 'No soil data available.');
            return $this->redirectToRoute('app_soil_statistics');
        }
    
        $idealConditions = [
            'pH' => [
                'min' => min(array_map(fn($s) => $s->getNiveauPh(), $soils)),
                'max' => max(array_map(fn($s) => $s->getNiveauPh(), $soils)),
                'average' => array_sum(array_map(fn($s) => $s->getNiveauPh(), $soils)) / count($soils),
            ],
            'Humidity' => [
                'min' => min(array_map(fn($s) => $s->getHumidite(), $soils)),
                'max' => max(array_map(fn($s) => $s->getHumidite(), $soils)),
                'average' => array_sum(array_map(fn($s) => $s->getHumidite(), $soils)) / count($soils),
            ],
            'Nutrient Level' => [
                'min' => min(array_map(fn($s) => $s->getNiveauNutriment(), $soils)),
                'max' => max(array_map(fn($s) => $s->getNiveauNutriment(), $soils)),
                'average' => array_sum(array_map(fn($s) => $s->getNiveauNutriment(), $soils)) / count($soils),
            ],
        ];
    
        return $this->render('crop/ideal_conditions.html.twig', [
            'idealConditions' => $idealConditions,
        ]);
    }

    #[Route('/soil/specificstats/{cropId}', name: 'app_crop_soil_statistics')]
public function cropSoilStatistics(int $cropId, SoildataRepository $soilRepository, CropRepository $cropRepository): Response
{
    // Fetch the crop by ID
    $crop = $cropRepository->find($cropId);
    if (!$crop) {
        throw $this->createNotFoundException('Crop not found.');
    }

    // Fetch only the soil data associated with the given crop
    $soilData = $soilRepository->findBy(['crop' => $crop]);

    // If no soil data exists, show an error
    if (empty($soilData)) {
        $this->addFlash('error', 'No soil data available for this crop.');
        return $this->redirectToRoute('soildata_affichage', ['id' => $cropId]);
    }

    // Prepare data for statistics
    $phLevels = array_map(fn($s) => $s->getNiveauPh(), $soilData);
    $humidityLevels = array_map(fn($s) => $s->getHumidite(), $soilData);
    $nutrientLevels = array_map(fn($s) => $s->getNiveauNutriment(), $soilData);

    // Count occurrences of soil types

    return $this->render('soildata/specificstats.html.twig', [
        'crop' => $crop, // Pass crop details
        'phLevels' => json_encode($phLevels),
        'humidityLevels' => json_encode($humidityLevels),
        'nutrientLevels' => json_encode($nutrientLevels),
    ]);
}

    
   }