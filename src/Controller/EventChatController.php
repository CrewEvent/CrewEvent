<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Update;
use App\Repository\ParticipantRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class EventChatController extends AbstractController
{
    #[Route('/event/{name}/chat', name: 'app_event_chat')]
    public function event_chat(Event $event, ParticipantRepository $participantRepo, Request $request, HubInterface $hub): Response
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


        //On crée le formulaire de message
        $form = $this->createFormBuilder()
            ->add(
                'message',
                TextType::class,
                ['attr' => ['autocomplete' => 'off', 'class' => 'form-control form-control-lg', 'placeholder' => 'entrer un message']]
            )

            ->getForm();

        //On le clone juste pour pouvoir le réutiliser
        $emptyForm = clone $form;

        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);


        $canal = strval($event->getId());
        //Si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            //On regarde si l'utilisateur est participant dans l'événement pour pouvoir envoyer des messages
            if ($isParticipant) {



                //On prend les données du formulaire cad le contenu du message
                $data = $form->getData();

                // 🔥 The magic happens here! 🔥
                // The HTML update is pushed to the client using Mercure

                $hub->publish(new Update(
                    $canal,
                    $this->renderView('chat/message.stream.html.twig', ['message' => $data['message'], 'sender' => $username]),
                    false,
                    null,
                    null,
                    2
                ));

                // Force an empty form to be rendered below
                // It will replace the content of the Turbo Frame after a post
                $form = $emptyForm;
            } else {
                //Sinon on le redirrige vers la pasge informations générales et lui afficher un message flash pour qu'il s'abonne  

                return $this->redirectToRoute('app_show_event', ['name' => $event->getName()]);
            }
        }
        

        //Affichage de la page
        return $this->renderForm('event_chat/event_chat.html.twig', [
            'form' => $form,
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant,
            'canal' => $canal
        ]);
    }
}