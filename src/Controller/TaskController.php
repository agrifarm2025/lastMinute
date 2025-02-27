<?php

namespace App\Controller;

use App\Entity\Field;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\FieldRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/taskcreate/{id}', name: 'task_create')]
    public function form(ManagerRegistry $m,Request $req,$id ) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $task = new Task(); 
        $Field = $em->getRepository(Field::class)->find($id);

        if (!$Field) {
            throw $this->createNotFoundException('Field not found');
        }
    
        // Set the Field for the new task entity
        $task->setField($Field);
        $task->setStatus("to do");
        $form=$this->createForm(TaskType::class,$task);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
        $task->getDate();
        $task->getDeadline();
        $task->setTotal();
        $outcome=$task->getField()->getOutcome()+$task->getTotal();


        $task->getField()->setOutcome($outcome);
        $em->persist($task);  
        $em->flush();  
       

        return $this->redirectToRoute('task', ['id' => $task->getField()->getId()]);
        }
        return $this->render("front/task/taskcreate.html.twig",[
            'form'=>$form
        ]);  
    } 
    #[Route('/deletetask/{id}', name: 'delete_task')]  
    public function deletetask(TaskRepository $rep,ManagerRegistry $m,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $task=$rep->find($id);
        
        $em->remove($task);  
        $em->flush();  
        return $this->redirectToRoute('task', ['id' => $task->getField()->getId()]);
        } 
        #[Route('/update-task-status/{id}/{status}', name: 'update_task_status', methods: ['GET'])]
public function updateTaskStatus(ManagerRegistry $doctrine, $id, $status): JsonResponse
{
    $em = $doctrine->getManager();
    $taskRepo = $em->getRepository(Task::class);
    $task = $taskRepo->find($id);

    if (!$task) {
        return new JsonResponse(['error' => 'Task not found'], 404);
    }

    $task->setStatus($status);
    $em->persist($task);
    $em->flush();

    return new JsonResponse(['success' => "Task status updated to '$status'"]);
}

#[Route('/cabin/{id}', name: 'cabin')]
public function cabin($id, ManagerRegistry $m)
{
    $em = $m->getManager();
    $field = $em->getRepository(Field::class)->find($id);

    if (!$field) {
        throw $this->createNotFoundException('Field not found.');
    }

    $tasks = [
        [
            'name' => 'Prepare Cabin Site',
            'description' => 'Clear and level the area for cabin construction.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '2 days',
            'workers' => 3,
            'payment_worker' => 60.0,
        ],
        [
            'name' => 'Cabin Foundation',
            'description' => 'Pour or lay the foundation for the cabin.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '3 days',
            'workers' => 4,
            'payment_worker' => 80.0,
        ],
        [
            'name' => 'Cabin Construction',
            'description' => 'Build the cabin structure (walls, roof, etc.).',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '5 days',
            'workers' => 5,
            'payment_worker' => 100.0,
        ],
        [
            'name' => 'Cabin Finishing',
            'description' => 'Install windows, doors, and interior finishing.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '3 days',
            'workers' => 3,
            'payment_worker' => 70.0,
        ],
    ];

    $today = new \DateTime();
    $baseStartDate = clone $today->modify('+1 day'); // Start tasks from tomorrow
    $taskOffset = 0;

    $this->createTasks($em, $field, $tasks, $baseStartDate, $taskOffset, 'Cabin materials');

    $em->flush(); // Flush the changes to the database

    return $this->redirectToRoute('task', ['id' => $field->getId()]); // Redirect to task list or details
}
#[Route('/fence/{id}', name: 'fence')]
public function fence($id, ManagerRegistry $m)
{
    $em = $m->getManager();
    $field = $em->getRepository(Field::class)->find($id);

    if (!$field) {
        throw $this->createNotFoundException('Field not found.');
    }

    $tasks = [
        // Your fence tasks here...
        [
            'name' => 'Clear Fence Line',
            'description' => 'Clear the area where the fence will be installed.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '1 day',
            'workers' => 3,
            'payment_worker' => 50.0,
        ],
        [
            'name' => 'Measure and Mark Fence Line',
            'description' => 'Measure and mark the fence line using stakes and string.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '1 day',
            'workers' => 2,
            'payment_worker' => 50.0,
        ],
        [
            'name' => 'Dig Post Holes',
            'description' => 'Dig holes for fence posts at marked locations.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '2 days',
            'workers' => 4,
            'payment_worker' => 75.0,
        ],
        [
            'name' => 'Set Fence Posts',
            'description' => 'Set fence posts in the holes and secure them with concrete.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '1 day',
            'workers' => 4,
            'payment_worker' => 75.0,
        ],
        [
            'name' => 'Attach Fence Panels',
            'description' => 'Attach fence panels to the posts using nails or screws.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '2 days',
            'workers' => 4,
            'payment_worker' => 75.0,
        ],
        [
            'name' => 'Inspect Fence Integrity',
            'description' => 'Inspect the fence for any weak spots or gaps and fix them.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '1 day',
            'workers' => 2,
            'payment_worker' => 50.0,
        ],
    ];

    $today = new \DateTime();
    $baseStartDate = clone $today->modify('+1 day');
    $taskOffset = 0;

    $this->createTasks($em, $field, $tasks, $baseStartDate, $taskOffset, 'Fence materials');

    $em->flush();
    return $this->redirectToRoute('task', ['id' => $field->getId()]);
}

#[Route('/water/{id}', name: 'water')]
public function water($id, ManagerRegistry $m)     
{
    $em = $m->getManager();
    $field = $em->getRepository(Field::class)->find($id);

    if (!$field) {
        throw $this->createNotFoundException('Field not found.');
    }

    $tasks = [
        // Your water tasks here...
        [
            'name' => 'Geological Survey',
            'description' => 'Conduct a survey to locate suitable underground water sources.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '2 days',
            'workers' => 2,
            'payment_worker' => 70.0,
        ],
        [
            'name' => 'Drilling and Well Construction',
            'description' => 'Drill the well and install casing and pump.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '4 days',
            'workers' => 3,
            'payment_worker' => 90.0,
        ],
        [
            'name' => 'Water Quality Testing',
            'description' => 'Test the water for quality and safety.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '1 day',
            'workers' => 1,
            'payment_worker' => 60.0,
        ],
    ];

    $today = new \DateTime();
    $baseStartDate = clone $today->modify('+1 day');
    $taskOffset = 0;

    $this->createTasks($em, $field, $tasks, $baseStartDate, $taskOffset, 'Well materials');

    $em->flush();
    return $this->redirectToRoute('task', ['id' => $field->getId()]);
}

#[Route('/irrigation/{id}', name: 'irrigation')]
public function irrigation($id, ManagerRegistry $m)     
{
    $em = $m->getManager();
    $field = $em->getRepository(Field::class)->find($id);

    if (!$field) {
        throw $this->createNotFoundException('Field not found.');
    }

    $tasks = [
        // Your irrigation tasks here...
        [
            'name' => 'Irrigation System Design',
            'description' => 'Design the irrigation layout and select appropriate equipment.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '2 days',
            'workers' => 2,
            'payment_worker' => 70.0,
        ],
        [
            'name' => 'Irrigation System Installation',
            'description' => 'Install pipes, sprinklers, and control systems.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '5 days',
            'workers' => 4,
            'payment_worker' => 80.0,
        ],
        [
            'name' => 'Irrigation System Testing',
            'description' => 'Test the system for leaks and proper operation.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '1 day',
            'workers' => 1,
            'payment_worker' => 60.0,
        ],
    ];

    $today = new \DateTime();
    $baseStartDate = clone $today->modify('+1 day');
    $taskOffset = 0;

    $this->createTasks($em, $field, $tasks, $baseStartDate, $taskOffset, 'Irrigation materials');

    $em->flush();
    return $this->redirectToRoute('task', ['id' => $field->getId()]);
}

#[Route('/photo/{id}', name: 'photo')]
public function photo($id, ManagerRegistry $m)     
{
    $em = $m->getManager();
    $field = $em->getRepository(Field::class)->find($id);

    if (!$field) {
        throw $this->createNotFoundException('Field not found.');
    }

    $tasks = [
        // Your photovoltaic tasks here...
        [
            'name' => 'Solar Panel Site Assessment',
            'description' => 'Assess the site for optimal sunlight exposure.',
            'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '1 day',
            'workers' => 1,
            'payment_worker' => 70.0,
        ],
        [
            'name' => 'Solar Panel Installation',
            'description' => 'Install solar panels and wiring.',
            'status' => 'to do',
            'priority' => 'High',
            'estimated_duration' => '3 days',
            'workers' => 3,
            'payment_worker' => 90.0,
        ],
        [
            'name' => 'Electrical Connection and Testing',
            'description' => 'Connect to the electrical grid and test the system',
'status' => 'to do',
            'priority' => 'Medium',
            'estimated_duration' => '2 days',
            'workers' => 2,
            'payment_worker' => 80.0,
        ],
    ];

    $today = new \DateTime();
    $baseStartDate = clone $today->modify('+1 day');
    $taskOffset = 0;

    $this->createTasks($em, $field, $tasks, $baseStartDate, $taskOffset, 'Photovoltaic materials');

    $em->flush();
    return $this->redirectToRoute('task', ['id' => $field->getId()]);
}


private function createTasks(EntityManagerInterface $em, Field $field, array $tasks, \DateTime $baseStartDate, int &$taskOffset, string $material): void
{
    $currentDate = clone $baseStartDate->modify('+' . $taskOffset . ' days');
    foreach ($tasks as $taskData) {
        $task = new Task();
        $startDate = clone $currentDate;
        $duration = (int)str_replace(' days', '', $taskData['estimated_duration']);
        $endDate = clone $currentDate->modify('+' . ($duration - 1) . ' days');

        $task->autoTask(
            $taskData['name'],
            $taskData['description'],
            $taskData['status'],
            $startDate,
            $material,
            'Farm Manager',
            $field,
            $taskData['priority'],
            $taskData['estimated_duration'],
            $endDate,
            $taskData['workers'],
            new \DateTime(),
            $taskData['payment_worker']
        );
        $em->persist($task);
        $currentDate->modify('+' . $duration . ' days');
        $taskOffset += $duration;
    }
}
}
