<?php

namespace App\Controller;

use App\Entity\Field;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\FieldRepository;
use App\Repository\TaskRepository;
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
    
}
