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
use Symfony\Component\Routing\Attribute\Route;

final class FarmController extends AbstractController
{
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
            $field = new Field(
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
    
            // Adding tasks based on farm attributes
            if (!$farm->isFence()) {
                $tasks = [
                    [
                        'name' => 'Clear Fence Line',
                        'description' => 'Clear the area where the fence will be installed',
                        'status' => 'to do',
                        'priority' => 'High',
                        'estimated_duration' => '1 day',
                        'workers' => 3,
                        'payment_worker' => 50.0
                    ],
                    [
                        'name' => 'Measure and Mark Fence Line',
                        'description' => 'Measure and mark the fence line using stakes and string',
                        'status' => 'to do',
                        'priority' => 'High',
                        'estimated_duration' => '1 day',
                        'workers' => 2,
                        'payment_worker' => 50.0
                    ],
                    [
                        'name' => 'Dig Post Holes',
                        'description' => 'Dig holes for fence posts at marked locations',
                        'status' => 'to do',
                        'priority' => 'High',
                        'estimated_duration' => '2 days',
                        'workers' => 4,
                        'payment_worker' => 75.0
                    ],
                    [
                        'name' => 'Set Fence Posts',
                        'description' => 'Set fence posts in the holes and secure them with concrete',
                        'status' => 'to do',
                        'priority' => 'High',
                        'estimated_duration' => '1 day',
                        'workers' => 4,
                        'payment_worker' => 75.0
                    ],
                    [
                        'name' => 'Attach Fence Panels',
                        'description' => 'Attach fence panels to the posts using nails or screws',
                        'status' => 'to do',
                        'priority' => 'High',
                        'estimated_duration' => '2 days',
                        'workers' => 4,
                        'payment_worker' => 75.0
                    ],
                    [
                        'name' => 'Inspect Fence Integrity',
                        'description' => 'Inspect the fence for any weak spots or gaps and fix them',
                        'status' => 'to do',
                        'priority' => 'Medium',
                        'estimated_duration' => '1 day',
                        'workers' => 2,
                        'payment_worker' => 50.0
                    ]
                ];
    
                foreach ($tasks as $taskData) {
                    $task = new Task(
                        $taskData['name'],
                        $taskData['description'],
                        $taskData['status'],
                        new \DateTime('tomorrow'),
                        'Fence materials',
                        'John Doe',
                        $field,
                        $taskData['priority'],
                        $taskData['estimated_duration'],
                        new \DateTime('+' . explode(' ', $taskData['estimated_duration'])[0] . ' days'),
                        $taskData['workers'],
                        new \DateTime('now'),
                        $taskData['payment_worker']
                    );
                    $em->persist($task);
                }
            }
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
    #[Route('/list/{id}', name: 'list')]  
    public function show_books(FarmRepository $farms,$id)  
{  
    $farm=$farms->find($id);
    $fields=$farms->getFieldJoin($farm);
    
    return $this->render('front/field/fieldtab.html.twig', [  
        'farm'=>$farm ,'fields'=>$fields
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
