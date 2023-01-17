<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository(Task::class)->findAll()]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // add Author for this task
            $user = $this->getUser() ;
            $task->setAuthor($user);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Request $request,$id)
    {

        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction($id)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(EntityManagerInterface $em,$id)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        $role = $this->getUser()->getRoles();
        $em = $this->getDoctrine()->getManager();

        // delete if admin and author of the same name or anonymous task
        if($role[0] == 'ROLE_ADMIN' && $task->getAuthor() == $this->getUser() || $task->getAuthor() == null){
            $em->remove($task);
            $em->flush();
            $this->addFlash('success', 'La tâche Annonyme a bien été supprimée.');
        }
        // remove the task of the same name
        elseif($role[0] == 'ROLE_USER' && $task->getAuthor() == $this->getUser()){

            $em->remove($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }
        // impossible
        else{

            $this->addFlash('success', 'Action impossible avec vos droits actuel');
        }

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/finished", name="task_finished")
     */
    public function task_finished()
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->findBy( array('isDone' => true) );
        return $this->render('task/list.html.twig', [
            'tasks' => $task,
        ]);
    }
}