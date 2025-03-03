<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Form\CropType;
use App\Repository\CropRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CropInfoService;
use App\Service\CropCalendarService;
use Knp\Component\Pager\PaginatorInterface;



class CropController extends AbstractController
{
    private $cropInfoService;
    private $cropCalendarService;

    public function __construct(CropInfoService $cropInfoService, CropCalendarService $cropCalendarService)
    {
        $this->cropInfoService = $cropInfoService;
        $this->cropCalendarService = $cropCalendarService;
    }

    #[Route('/crop/add', name: 'app_crop_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, CropCalendarService $cropCalendarService): Response
    {
        $crop = new Crop();
    
        // Fetch crop types & AEZs from FAO API
        $cropCalendarData = $cropCalendarService->getCropsForTunisia();
        $cropChoices = [];
    
        foreach ($cropCalendarData as $cropData) {
            if (isset($cropData['crop']['name'])) {
                $cropChoices[] = $cropData['crop']['name'];
            }
        }
    
        // Create the form with dynamic crop choices
        $form = $this->createForm(CropType::class, $crop, [
            'crop_choices' => $cropChoices,
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $plantationDate = $crop->getDatePlantation();
            $harvestDate = $crop->getDateCrop();
    
            // Validate that plantation and harvest dates are provided
            if (!$plantationDate || !$harvestDate) {
                $this->addFlash('error', 'Veuillez fournir les dates de plantation et de récolte.');
                return $this->redirectToRoute('app_crop_add');
            }
    
            // Fetch crop calendar data for validation
            $matchedCrop = null;
            foreach ($cropCalendarData as $cropData) {
                if ($cropData['crop']['name'] === $crop->getTypeCrop()) {
                    $matchedCrop = $cropData;
                    break;
                }
            }
    
            if ($matchedCrop && isset($matchedCrop['sessions'])) {
                $validPlantation = false;
                $validHarvest = false;
                $earlySowingDates = [];
                $lateSowingDates = [];
                $earlyHarvestDates = [];
                $lateHarvestDates = [];
    
                foreach ($matchedCrop['sessions'] as $session) {
                    // 🌟 Check if the keys exist before accessing them
                    if (isset($session['early_sowing']['month'], $session['early_sowing']['day'])) {
                        $earlySowing = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['early_sowing']['month'], $session['early_sowing']['day']));
                        $earlySowingDates[] = $earlySowing->format('d-m-Y');
                    } else {
                        continue; // Skip session if essential data is missing
                    }
    
                    if (isset($session['later_sowing']['month'], $session['later_sowing']['day'])) {
                        $lateSowing = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['later_sowing']['month'], $session['later_sowing']['day']));
                        $lateSowingDates[] = $lateSowing->format('d-m-Y');
                    } else {
                        $lateSowing = $earlySowing; // Fallback: Use early date if late date is missing
                    }
    
                    if (isset($session['early_harvest']['month'], $session['early_harvest']['day'])) {
                        $earlyHarvest = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['early_harvest']['month'], $session['early_harvest']['day']));
                        $earlyHarvestDates[] = $earlyHarvest->format('d-m-Y');
                    } else {
                        continue; // Skip session if essential data is missing
                    }
    
                    if (isset($session['later_harvest']['month'], $session['later_harvest']['day'])) {
                        $lateHarvest = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['later_harvest']['month'], $session['later_harvest']['day']));
                        $lateHarvestDates[] = $lateHarvest->format('d-m-Y');
                    } else {
                        $lateHarvest = $earlyHarvest; // Fallback: Use early harvest date if late is missing
                    }
    
                    // Check if plantation date is valid
                    if ($plantationDate >= $earlySowing && $plantationDate <= $lateSowing) {
                        $validPlantation = true;
                    }
    
                    // Check if harvest date is valid
                    if ($harvestDate >= $earlyHarvest && $harvestDate <= $lateHarvest) {
                        $validHarvest = true;
                    }
                }
    
                // 🚨 Show error for invalid plantation date
                if (!$validPlantation) {
                    return $this->render('crop/error_date.html.twig', [
                        'plantationDate' => $plantationDate->format('d-m-Y'),
                        'earlySowingDates' => $earlySowingDates,
                        'lateSowingDates' => $lateSowingDates,
                        'cropName' => $crop->getTypeCrop(),
                        'errorType' => 'plantation'
                    ]);
                }
    
                // 🚨 Show error for invalid harvest date
                if (!$validHarvest) {
                    return $this->render('crop/error_date.html.twig', [
                        'plantationDate' => $harvestDate->format('d-m-Y'),
                        'earlySowingDates' => $earlyHarvestDates,
                        'lateSowingDates' => $lateHarvestDates,
                        'cropName' => $crop->getTypeCrop(),
                        'errorType' => 'harvest'
                    ]);
                }
            }
    
            // Save the crop if both dates are valid
            $entityManager->persist($crop);
            $entityManager->flush();
    
            $this->addFlash('success', 'Culture ajoutée avec succès !');
            return $this->redirectToRoute('crop_affichage');
        }
    
        return $this->render('crop/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
    


#[Route('/updatecrop/{id}', name: 'app_updateformcrop', methods: ['GET', 'POST'])]
public function updateformcrop(Request $request, EntityManagerInterface $entityManager, CropRepository $cropRepository, CropCalendarService $cropCalendarService, int $id): Response
{
    $crop = $cropRepository->find($id);

    if (!$crop) {
        throw $this->createNotFoundException("Crop not found");
    }

    // Récupérer les cultures disponibles via l'API FAO
    $cropCalendarData = $cropCalendarService->getCropsForTunisia();
    $cropChoices = [];

    foreach ($cropCalendarData as $cropData) {
        if (isset($cropData['crop']['name'])) {
            $cropChoices[] = $cropData['crop']['name'];
        }
    }

    // Créer le formulaire avec les cultures dynamiques
    $form = $this->createForm(CropType::class, $crop, [
        'crop_choices' => $cropChoices,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plantationDate = $crop->getDatePlantation();

        // Vérifier que la date de plantation est bien fournie
        if (!$plantationDate) {
            $this->addFlash('error', 'Veuillez fournir une date de plantation.');
            return $this->redirectToRoute('app_updateformcrop', ['id' => $id]);
        }

        // Recherche de la culture correspondante
        $matchedCrop = null;
        foreach ($cropCalendarData as $cropData) {
            if ($cropData['crop']['name'] === $crop->getTypeCrop()) {
                $matchedCrop = $cropData;
                break;
            }
        }

        if ($matchedCrop && isset($matchedCrop['sessions'])) {
            $validDate = false;
            $earlySowingDates = [];
            $lateSowingDates = [];

            foreach ($matchedCrop['sessions'] as $session) {
                $earlySowing = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['early_sowing']['month'], $session['early_sowing']['day']));
                $lateSowing = new \DateTime(sprintf('%04d-%02d-%02d', date('Y'), $session['later_sowing']['month'], $session['later_sowing']['day']));

                $earlySowingDates[] = $earlySowing->format('d-m-Y');
                $lateSowingDates[] = $lateSowing->format('d-m-Y');

                if ($plantationDate >= $earlySowing && $plantationDate <= $lateSowing) {
                    $validDate = true;
                    break;
                }
            }

            if (!$validDate) {
                return $this->render('crop/error_date.html.twig', [
                    'plantationDate' => $plantationDate->format('d-m-Y'),
                    'earlySowingDates' => $earlySowingDates,
                    'lateSowingDates' => $lateSowingDates,
                    'cropName' => $crop->getTypeCrop()
                ]);
            }
        }

        // Mise à jour du crop si la date est valide
        $entityManager->persist($crop);
        $entityManager->flush();

        $this->addFlash('success', 'Crop mis à jour avec succès !');
        return $this->redirectToRoute('crop_affichage');
    }

    return $this->render('crop/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route("/crop/affichage", name: "crop_affichage")]
    public function affichage(CropRepository $em,PaginatorInterface $paginator,Request $request): Response
    {
        $crops = $em->findAll();
        $queryBuilder = $em->createQueryBuilder('c')->getQuery();

    // Paginate the query
    $pagination = $paginator->paginate(
        $queryBuilder, // Query to paginate
        $request->query->getInt('page', 1), // Current page number, default to 1
        10 // Number of items per page
    );
        $countries = [
            ['code' => 'TN', 'name' => 'Tunisia'],
        ];
        return $this->render('crop/affichage.html.twig', [
            'crop' => $crops,
            'countries' => $countries,
            'pagination' => $pagination, // Pass the pagination object to the template

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

    #[Route('/crop', name: 'app_crop')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $crops = $entityManager->getRepository(Crop::class)->findAll();

        return $this->render('crop/backcropaffiche.html.twig', [
            'crops' => $crops,
        ]);
    }

    #[Route('/crop/recherche', name: 'recherche_crop', methods: ['GET'])]
    public function rechercherCrops(Request $request, CropRepository $cropRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $queryBuilder = $cropRepository->createQueryBuilder('c');

        if (!empty($searchTerm)) {
            $queryBuilder
                ->where('c.type_crop LIKE :searchTerm')
                ->orWhere('c.crop_event LIKE :searchTerm')
                ->setParameter('searchTerm', "%" . $searchTerm . "%");
        }

        $crops = $queryBuilder->getQuery()->getResult();

        return $this->render('crop/affichage.html.twig', [
            'crop' => $crops,
        ]);
    }

    #[Route('/crop/trier', name: 'trier_crop', methods: ['GET'])]
    public function trierCrops(Request $request, CropRepository $cropRepository): Response
    {
        $sort = $request->query->get('sort', 'type_crop');
        $order = $request->query->get('order', 'asc');

        $queryBuilder = $cropRepository->createQueryBuilder('c');
        $queryBuilder->orderBy('c.' . $sort, $order);

        $crops = $queryBuilder->getQuery()->getResult();

        return $this->render('crop/affichage.html.twig', [
            'crop' => $crops,
        ]);
    }

    #[Route('/crop/crop_Info', name: 'crop_Info')]
    public function cropInfo(
        CropInfoService $cropInfoService,
        PaginatorInterface $paginator, // Added paginator
        Request $request // Needed for pagination
    ): Response {
        $error = null;
    
        try {
            $cropData = $cropInfoService->getCropsForTunisia();
        } catch (\Exception $e) {
            $cropData = [];
            $error = $e->getMessage();
        }
    
        // Paginate the crop data
        $pagination = $paginator->paginate(
            $cropData, // Data to paginate
            $request->query->getInt('page', 1), // Current page number
            10 // Items per page
        );
    
        return $this->render('crop/crop_info.html.twig', [
            'pagination' => $pagination,
            'error' => $error,
        ]);
    }
    
    

    #[Route('/crop/crop_calendar', name: 'crop_calendar')]
    public function cropCalendar(
        CropRepository $cropRepository,
        CropCalendarService $cropCalendarService,
        PaginatorInterface $paginator, // Add PaginatorInterface
        Request $request // Add Request to handle pagination
    ): Response
    {
        $error = null;
    
        // Fetch crop calendar data
        try {
            $cropCalendarData = $cropCalendarService->getCropsForTunisia();
        } catch (\Exception $e) {
            $cropCalendarData = [];
            $error = $e->getMessage();
        }
    
        // Paginate the crop calendar data
        $pagination = $paginator->paginate(
            $cropCalendarData, // Data to paginate
            $request->query->getInt('page', 1), // Current page number, default to 1
            10 // Number of items per page
        );
    
        return $this->render('crop/crop_calendar.html.twig', [
            'pagination' => $pagination, // Pass the paginated data to the template
            'error' => $error
        ]);
    }
}


