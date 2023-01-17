<?php

namespace App\Controller;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class DefaultController extends AbstractController
{
    #[Route("/order/{maVar}", name:'test.order.router')]
    public function testOrderRoute($maVar){
        return new Response($maVar);
    }


    #[Route("/", name:"homepage")]
    
    public function index():Response
    {
        return $this->render('default/index.html.twig');
    }

    #[Route("/first/{name}", name:"first")]
    
    public function seyHello($name):Response
    {
        return $this->render('first/index.html.twig',[
            'nom' => $name ,
            'firstname' => 'Christophe'
        ]);
    }
    #[Route(
        '/multi/{entier1<\d+>}/{entier2<\d+>}',
        name: 'multiplication'
        )]
   public function multiplication($entier1, $entier2){
    $resultat = $entier1*$entier2;

    return new Response("<h1>$resultat</h1>");
   }
}