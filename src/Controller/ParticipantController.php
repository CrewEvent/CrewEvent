<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;

/*
Controller pour gérer la partie événement.
*/

class ParticipantController extends AbstractController
{

    /*
    Ajout d'un participant dans l'événement
    */
    #[Route('/add_participant/{name}', name: 'app_add_participant')]
    public function add_participant(Event $event, EntityManagerInterface $em): Response
    {
        //On crée un nouveau objet participant
        $participant = new Participant();

        //On prend les données de l'utilisateur connecté
        $user = $this->getUser();

        //On met le participant dans l'événement
        $participant->setEvent($event);

        //On enregistre le nom de l'événement dans la table Participants
        $participant->setEventName($event->getName());

        //On enregistre le nom d'utilisateur dans le repo participant
        $participant->setParticipantUsername($user->getUserIdentifier());

        //On enregistre le participant Id
        $participant->setUserId($user->getId());

        //On enregistre dans la base de donnée
        $em->persist($participant);
        $em->flush();

        //On redirrigige vers la page show_event
        return $this->redirectToRoute("app_show_event", [
            'name' => $event->getName(),
        ]);
    }





}