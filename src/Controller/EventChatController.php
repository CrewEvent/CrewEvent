<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\ChatType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Update;
use App\Repository\ParticipantRepository;
use App\Entity\ChatEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChatEventRepository;

class EventChatController extends AbstractController
{
    #[Route('/event/{name}/chat', name: 'app_event_chat')]
    public function event_chat(Event $event, ParticipantRepository $participantRepo, Request $request, EntityManagerInterface $em, ChatEventRepository $chatRepo): Response
    {
        //On récupére l'identifiant de l'utilisateur connecté
        $username = $this->getUser()->getUserIdentifier();

        //Cherche si l'utilisateur est déja participant
        // $isParticipant = $this->search_if_participant($username, $event->getName());

        //On cherche si l'utilisateur est déja participant
        $isParticipant = false;

        //On prend tous les objets participants qui ont pour attribut le nom de l'événement
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);

        //Pour chque objet on regarde si le nom d'utilisateur est celui de l'utilisateur connecté
        foreach ($participants as $participant) {
            if ($participant->getParticipantUsername() == $username) {
                $isParticipant = true;
            }
        }

        //On crée un nouveau Message
        $chatEvent = new ChatEvent();
        $form = $this->createForm(ChatType::class, $chatEvent);



        //On dit au formulaie de gérer la requéte
        $form->handleRequest($request);

        //Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On définit l'envoyeur et le l'événement concerné
            $chatEvent->setSender($username);
            $chatEvent->setEvent($event);

            //On enregistre dans la base de donnée
            $em->persist($chatEvent);
            $em->flush();

            //Redirection vers la méme page
            // $this->redirectToRoute('app_event_chat', [
            //     'event' => $event,
            //     'name' => $event->getName(),
            //     'participants' => $participants,
            //     'isParticipant' => $isParticipant,
            //     'form' => $form->createView(),
            //     //On lui donne le chat aussi à afficher
            //     'eventChats' => $eventChats
            // ]);
        }

        //On récupére le chat de l'événement

        $messages = $chatRepo->findBy(['event' => $event]);
        //retourne la page
        return $this->render('event_chat/event_chat.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant,
            'form' => $form->createView(),
            'messages' => $messages

        ]);
    }
}