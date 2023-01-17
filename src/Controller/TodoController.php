<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route("/todo")]
class TodoController extends AbstractController
{
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session= $request->getSession();

        if(!$session->has('todos')){
            $todos = [
                'achat'=>'acheter clé USB',
                'cours'=>'Finaliser mon cours',
                'correction'=>'corriger mes examens'
        ];
        $session->set('todos', $todos);
        $this->addFlash('info',"la liste des todos vient d'être initialiée");
        } //sinon
            // afficher une erreur rediriger vers les controller index
        return $this->render('todo/index.html.twig');
    }

    #[Route(
        '/add/{name?sf7}/{content?test}', 
        name:'todo.add',
        
        )]
    public function addTodo(Request $request, $name, $content): RedirectResponse {
        $session = $request->getSession();

       // vérifier si j'ai mon tableau todo dans ma session
       if ($session->has('todos')){
         // si oui 
           //vérifier  si on a déjà  un todo avec le même name ->erreur
           $todos=$session->get('todos');
           if(isset($todos[$name])){
            $this->addFlash('error',"le todo d'id $name existe déjà dans la liste");
       }else {
         //si non on l'ajoute et on affiche un message de succès

         $todos[$name]= $content;
            $session->set('todos', $todos);
$this->addFlash('success',"le todo été ajouté avec succès");
       }
           }

                //si oui afficher erreur
                //si non on l'ajoute et on affiche un message de succès

       else{
 //sinon
            // afficher une erreur rediriger vers les controller index
            $this->addFlash('error',"la liste des todos n'a pas encore été initialisée");
       }

       return $this->redirectToRoute('todo');
    }
   

    #[Route('/update/{name}/{content}', name:'todo.update')]
    public function updateTodo(Request $request, $name, $content): RedirectResponse{
        $session = $request->getSession();

       // vérifier si j'ai mon tableau todo dans ma session
       if ($session->has('todos')){
         // si oui 
           //vérifier  si on a déjà  un todo avec le même name ->erreur
           $todos=$session->get('todos');
           if(!isset($todos[$name])){
            $this->addFlash('error',"le todo d'id $name n'existe pas dans la liste");
       }else {
         //si non on l'ajoute et on affiche un message de succès

         $todos[$name]= $content;
            $session->set('todos', $todos);
$this->addFlash('success',"le todo été mis à jour  avec succès");
       }
           }

                //si oui afficher erreur
                //si non on l'ajoute et on affiche un message de succès

       else{

            // afficher une erreur rediriger vers les controller index
            $this->addFlash('error',"la liste des todos n'a pas encore été initialisée");
       }

       return $this->redirectToRoute('todo');
    }
 
    #[Route('/delete/{name}', name:'todo.delete')]
    public function deleteTodo(Request $request, $name): RedirectResponse{
        $session = $request->getSession();

       // vérifier si j'ai mon tableau todo dans ma session
       if ($session->has('todos')){
         // si oui 
           //vérifier  si on a déjà  un todo avec le même name ->erreur
           $todos=$session->get('todos');
           if(!isset($todos[$name])){
            $this->addFlash('error',"le todo d'id $name existe déjà dans la liste");
       }else {
         
         unset($todos[$name]);
            $session->set('todos', $todos);
        $this->addFlash('success',"le todo été supprimé avec succès");
       }
      }   else{

            // afficher une erreur rediriger vers les controller index
            $this->addFlash('error',"la liste des todos n'a pas encore été initialisée");
       }

       return $this->redirectToRoute('todo');
    }
    
    #[Route('/reset', name:'todo.reset')]
    public function resetTodo(Request $request): RedirectResponse{
        $session = $request->getSession();
        $session->remove('todos');

       return $this->redirectToRoute('todo');
    }
}
