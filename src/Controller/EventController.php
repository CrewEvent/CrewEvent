<?php

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventCreationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
Controller pour la gestion d'événement
*/ 

class EventController extends AbstractController
{
    #[Route('/event/creation', name: 'app_event_creation', methods: ['POST','GET'])]
    public function event_creation(Request $request, EntityManagerInterface $em): Response
    {
        //on récupére les infos de l'utilisateur
        $user = $this->getUser();

        //On crée un nouveau événement
        $event = new Event;

        //On crée une nouvelle formulaire de création d'événement
        $form = $this->createForm(EventCreationType::class, $event);


        //on dit au formulaire de gérer les requettes
        $form->handleRequest($request);

        //Si le formulaire est soumis et valid

        if($form->isSubmitted() && $form->isValid()){

            //Donner un utilisateur à l'événement
            $event->setUser($user);
            //Enregistrement dans la base de donnée
            $em->persist($event);
            $em->flush();
            //redirection vers la page de l'événement
            return $this->redirectToRoute('app_show_event');
        }

        return $this->render('pages/event/event_creation.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/event/show/{name}', name: 'app_show_event', methods: ['POST','GET'])]
    public function event_show(Request $request, EntityManagerInterface $em, Event $event): Response
    {

        return $this->render('pages/event/event_show.html.twig',[
            'event' => $event
        ]);
    }

}
