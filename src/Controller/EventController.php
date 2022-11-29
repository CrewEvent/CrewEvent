<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Participant;
use App\Form\EventCreationType;
use App\Repository\ParticipantRepository;
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
    #[Route('/event/creation', name: 'app_event_creation', methods: ['POST', 'GET'])]
    public function event_creation(Request $request, EntityManagerInterface $em): Response
    {
        //on récupére les infos de l'utilisateur
        $user = $this->getUser();

        //On crée un nouveau événement
        $event = new Event;
        //On crée un nouveau formulaire de création d'événement
        $form = $this->createForm(EventCreationType::class, $event);


        //on dit au formulaire de gérer les requettes
        $form->handleRequest($request);

        //Si le formulaire est soumis et valid

        if ($form->isSubmitted() && $form->isValid()) {

            //Donner un utilisateur à l'événement
            $event->setUser($user);
            //Enregistrement dans la base de donnée
            $em->persist($event);
            $em->flush();
            //redirection vers la page de l'événement
            //on récupére le nom dans le formulaire qu'on donne en paramétre à app_show_event   
            return $this->redirectToRoute('app_add_participant', ['name' => $form->get('name')->getData()]);
        }

        //ça affiche page de création d'événement
        return $this->render('pages/event/event_creation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //Page de l'événement
    #[Route('/event/show/{name}', name: 'app_show_event', methods: ['POST', 'GET'])]
    public function event_show(Event $event, ParticipantRepository $participantRepo): Response
    {

        //On récupére l'identifiant de l'utilisateur connecté
        $username = $this->getUser()->getUserIdentifier();

        //Cherche si l'utilisateur est déja participant
        // $isParticipant = $this->search_if_participant($username, $event->getName());

        $isParticipant = false;

        //On prend tous les objets participants qui ont pour attribut le nom de l'événement
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);

        //Pour chque objet on regarde si le nom d'utilisateur est celui de l'utilisateur connecté
        foreach ($participants as $participant) {
            if ($participant->getParticipantUsername() == $username) {
                $isParticipant = true;
            }
        }
        //retourne la page
        return $this->render('pages/event/event_show.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant

        ]);
    }

    //Fonction recherche si l'utilisateur est déja participant à l'événement
    // public function search_if_participant(String $username, String $event_name)
    // {

    //     $ParticipantRepo = $this->getDoctrine()->getRepository(Participant::class);
    //     $participants = $ParticipantRepo->findAll();

    //     foreach ($participants as $participant) {
    //         if ($participant->getParticipantUsername() == $username && $participant->getEventName() == $event_name) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }
    // }
}
