<?php
namespace App\Controller;

use App\Entity\Farm;
use App\Entity\Field;
use App\Entity\Task;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WeatherService;
use App\Controller\WeatherController;
use Twilio\Rest\Client;


class FarmController extends AbstractController
{
    private $weatherService;
    private $weatherController;
    private $twilioClient;

    public function __construct(WeatherService $weatherService, WeatherController $weatherController,Client $twilioClient)
    {
        $this->weatherService = $weatherService;
        $this->weatherController = $weatherController;
        $this->twilioClient = $twilioClient;

    }
    public function sendSms($farm)
    {

        try {
            $message = $this->twilioClient->messages->create(
                '+21655771406', // to
                [
                    'from' => '+14347710556',
                    'body' => 'Alert from Farming Assistant: Catastrophic weather detected - '. $farm->getWeather(),
                ]
            );

        
        } catch (\Exception $e) {
            return new Response('Failed to send SMS: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function postpone($fields, $em)
    {
        foreach ($fields as $field) {
            $tasks = $field->getTasks();
            foreach ($tasks as $task) {
                $task->setDate($task->getDate()->modify('+3 day'));
                $task->setDeadline($task->getDeadline()->modify('+3 day'));
                $em->persist($task);
            }
    
            if ($field->getName() == 'Main Field') {
                $soilTreatmentTask = new Task();
                $soilTreatmentTask->autoTask(
                    'Soil Treatment',
                    'Soil treatment after rain',
                    'to do',
                    new \DateTime('tomorrow'),
                    'farm',
                    'John Doe', // Example responsible person
                    $field,
                    'high',
                    '3 days',
                    new \DateTime('tomorrow + 3 days'),
                    5, // Example number of workers
                    new \DateTime('now'),
                    100.0 // Example payment per worker
                );
                $field->addTask($soilTreatmentTask);

                $em->persist($soilTreatmentTask);
                $em->persist($field);


            }
    
            $em->persist($field);
        }
    
        $em->flush();
    }
    #[Route('/list/{id}', name: 'list')]
    public function show_fields(FarmRepository $farms, $id,ManagerRegistry $m)
    {
        $farm = $farms->find($id);
        $fields = $farms->getFieldJoin($farm);
        $em = $m->getManager();
        $lat = $farm->getLat();
        $lon = $farm->getLon();

        $forecast = $this->weatherService->getForecastByCoordinates(4, 50);
        

        $weather = $forecast['list'][0]['weather'];
        $description = $forecast['list'][0]['weather'][0]['description'];
        $temperature = $forecast['list'][0]['main']['temp'];
        $farm->setWeather($description);
        $em->persist($farm);
        $em->flush();
        $zone="Not Found";
        $aezData = [
            ["id" => "001", "name" => "Kroumirie - Mogods", "lat_min" => 36.0, "lat_max" => 37.5, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "002", "name" => "Sub-humid", "lat_min" => 35.0, "lat_max" => 36.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "003", "name" => "Semiarid fresh winter", "lat_min" => 34.0, "lat_max" => 35.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "004", "name" => "North East and Cap Bon (semi arid with mild winters)", "lat_min" => 33.0, "lat_max" => 34.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "006", "name" => "Lower Steppe (arid with mild winters)", "lat_min" => 32.0, "lat_max" => 33.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "012", "name" => "Dorsal and Tell (Kairouan)", "lat_min" => 31.0, "lat_max" => 32.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "013", "name" => "Bean", "lat_min" => 30.0, "lat_max" => 31.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "014", "name" => "Féverole", "lat_min" => 29.0, "lat_max" => 30.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "015", "name" => "Upper semi-arid", "lat_min" => 28.0, "lat_max" => 29.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "016", "name" => "Sidi Bouzid", "lat_min" => 27.0, "lat_max" => 28.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "017", "name" => "Sub-wet and semi arid", "lat_min" => 26.0, "lat_max" => 27.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "018", "name" => "Irrigated areas in the North", "lat_min" => 25.0, "lat_max" => 26.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "019", "name" => "Hot zone with mild winters", "lat_min" => 24.0, "lat_max" => 25.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "020", "name" => "Irrigated areas of Cap Bon and Central", "lat_min" => 23.0, "lat_max" => 24.0, "lon_min" => 8.0, "lon_max" => 11.5],
            ["id" => "021", "name" => "Irrigated areas in central and Cap Bon", "lat_min" => 22.0, "lat_max" => 23.0, "lon_min" => 8.0, "lon_max" => 11.5]
        ];
                foreach ($aezData as $aez) {
            if ($lat >= $aez['lat_min'] && $lat <= $aez['lat_max'] && $lon >= $aez['lon_min'] && $lon <= $aez['lon_max']) {
                $zone= $aez['id'];
            }
            
        }
    
        $warning = $this->weatherController->checkForCatastrophicWeather($forecast);
        if ($warning) {
            $this->sendSms($farm);
            $this->postpone($fields, $em);
        }
        return $this->render('front/field/fieldtab.html.twig', [
            'zone' => $zone,
            'farm' => $farm,
            'fields' => $fields,
            'description' => $description,
            'temperature' => $temperature,
            'warning' => $warning ?? null,
            'weather'=>$weather[0]
        ]);
    }
       #[Route('/farm', name: 'farm')]
    public function farm(ManagerRegistry $m, Request $req): Response
    {  
        $em = $m->getManager();  
        $farm = new Farm();  
        $form = $this->createForm(FarmType::class, $farm);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($farm);

            // Creating a field associated with the farm
            $field = new Field();
            $field->autoField(
                1000.0, // surface
                'Main Field', // name
                $farm, // farm
                5000.0, // budget
                0.0, // income
                0.0, // outcome
                0.0, // profit
                'farm chores', // description
                null // crop
            );
    
            $em->persist($field);
    
            //$this->water($field, $em, $farm);
            $em->flush();
            return $this->redirectToRoute('farmtab');
        }

        return $this->render("front/farm/farmcreate.html.twig",[
            'form' => $form->createView()
        ]);  
    }  

    #[Route('/farmtab', name: 'farmtab')]
    public function farmdisplay(FarmRepository $farm)
    {
        $farmdb=$farm->findall();
        return $this->render("front/farm/farmtab.html.twig", [
            "tab"=> $farmdb
            
        ]);


    }
   
#[Route('/deletefarm/{id}', name: 'delete_farm')]  
    public function deletefarm(FarmRepository $rep,ManagerRegistry $m,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $farm=$rep->find($id);
        
        $em->remove($farm);  
        $em->flush();  
        return $this->redirectToRoute('farmtab');        }
        
    #[Route('/farmupdate/{id}', name: 'farm_update')]
    public function updatefield(ManagerRegistry $m,Request $req,FarmRepository $rep ,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $farm=$rep->find($id); 
        $form=$this->createForm(FarmType::class,$farm);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $em->persist($farm);  
        $em->flush();  
        return $this->redirectToRoute('farmtab');
        }
        return $this->render("front/farm/farmupdate.html.twig",[
            'form'=>$form
        ]);  
    } 
    
}
