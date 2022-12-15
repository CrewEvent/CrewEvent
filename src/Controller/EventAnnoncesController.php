<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Repository\ParticipantRepository;

class EventAnnoncesController extends AbstractController
{
    //Partie annonces de l'événement
    #[Route('/event/show/{name}/news', name: 'app_event_news', methods: ['POST', 'GET'])]
    public function event_news(Event $event, ParticipantRepository $participantRepo): Response
    {
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);
        return $this->render('evenement/news.html.twig', [
            'event' => $event,
            'participants' => $participants
        ]);
    }
}
