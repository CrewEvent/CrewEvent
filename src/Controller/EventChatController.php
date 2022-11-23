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
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EventChatController extends AbstractController
{
    #[Route('/event/{name}/chat', name: 'app_event_chat')]
    public function event_chat(Event $event, ParticipantRepository $participantRepo, Request $request, EntityManagerInterface $em, ChatEventRepository $chatRepo, HubInterface $hub, Discovery $discovery, Authorization $authorization): Response
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

        //On créé un nouveau message
        $chatEvent = new ChatEvent;


        //On crée le formulaire de message
        $form = $this->createFormBuilder()
            ->add('message', TextType::class, ['attr' => ['autocomplete' => 'off']])
            ->add('send', SubmitType::class)
            ->getForm();

        //Aprés un post on vide le contenu du formulaire
        $emptyForm = clone $form;

        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);



        //Si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {

            //On prend les données du formulaire cad le contenu du message
            $data = $form->getData();

            //On fait des enregistrements pour la bdd
            $chatEvent->setContent($data['message']);
            $chatEvent->setSender($username);
            $chatEvent->setEvent($event);

            //On envoie dans la bdd
            $em->persist($chatEvent);
            $em->flush();


            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure

            $hub->publish(new Update(
                'chat',
                $this->renderView('chat/message.stream.html.twig', ['message' => $data['message']])
            ));

            // Force an empty form to be rendered below
            // It will replace the content of the Turbo Frame after a post
            $form = $emptyForm;
        }


        //Affichage de la page
        return $this->renderForm('event_chat/event_chat.html.twig', [
            'form' => $form,
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant
        ]);
    }
}