<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Repository\ParticipantRepository;
use Symfony\UX\Turbo\TurboBundle;

class EventAnnoncesController extends AbstractController
{
    //Partie annonces de l'événement
    #[Route('/event/show/{name}/news', name: 'app_event_news', methods: ['POST', 'GET'])]
    public function event_news(Event $event, ParticipantRepository $participantRepo): Response
    {
        //On prend tous les objets participants qui ont pour attribut le nom de l'événement
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);

        $isParticipant = false;
        //Pour chque objet on regarde si le nom d'utilisateur est celui de l'utilisateur connecté
        foreach ($participants as $participant) {
            if ($participant->getParticipantUsername() == $this->getUser()->getUserIdentifier()) {
                $isParticipant = true;
            }
        }

        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);
        return $this->render('evenement/news.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'isParticipant' =>$isParticipant
        ]);
    }

    //Partie Ajout de nouvelles infos générales
    #[Route('/event/annonce/add/{name}', name: 'event_annonce_add', methods: ['POST', 'GET'])]
    public function event_annonce_add(Event $event, Request $request, EntityManagerInterface $em)
    {
        //On récupére d'abord les donnéees
        $data = $request->query->all();


        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event->setAnnonces($values);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('annonces/annonce_add.stream.html.twig', ['id' => $event->getId(),'event' => $event, 'annonce' => $values]);

    }


    //Partie suppression de l'info
    #[Route('/event/delete/annonce/{name}/{annonce_title}', name: 'app_event_delete_annonce', methods: ['POST', 'GET'])]
    public function event_annonce_delete(Event $event, string $annonce_title, EntityManagerInterface $em, Request $request)
    {

        //On recherche la clé du titre dans la liste des infos avec arra_search():key
        //On supprime avaec unset(key)

        //On récupére toutes les annonces
        $annonces = $event->getAnnonces();

        //On innitialise le compteur
        $i = 0;
        //On parcours la table infos pour chercher title
        foreach ($annonces as $elt){
            //Si on trouve on récupére la clé dans res de la table $info
            if(array_search($annonce_title, $elt )){
                $res = $i;
            }
            $i ++;
        }

        //Puis on supprime et on enregistre
        $event = $event->deleteAnnonce($res);
        $em->persist($event);
        $em->flush();


        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('annonces/annonce_remove.stream.html.twig', ['title' => $annonce_title, ]);


        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!

    }


    #[Route('/event/annonce/update/{name}/{annonce_title}', name: 'app_event_update_annonce', methods: ['POST', 'GET'])]
    public function event_update_annonce(Event $event, string $annonce_title, Request $request)
    {

        $annonces = $event->getAnnonces();

        $i = 0;

        foreach ($annonces as $elt){
            if(array_search($annonce_title, $elt )){
                $res = $i;
            }
            $i ++;
        }
        //On récupre d'abord le contenu qui a pour titre info_title
        $annonce_content = $annonces[$res]['content'];

        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('annonces/annonce_update.stream.html.twig', ['event' => $event,'title' => $annonce_title, 'content' => $annonce_content, 'index'=>$res]);

        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!
    }

    #[Route('/event/annonce/update/success/{name}/{index}', name: 'event_annonce_update_success', methods: ['POST', 'GET'])]
    public function event_annonce_update_success(Event $event, int $index, EntityManagerInterface $em, Request $request)
    {
        $data = $request->query->all();

        //On récupére d'abord les donnéees
        $data = $request->query->all();

        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event = $event->updateAnnonce($values, $index);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_event_news',['name' => $event->getName()]);

    }



    #[Route('/event//annonce/update/cancel/{name}', name: 'event_annonce_update_cancel', methods: ['POST', 'GET'])]
    public function event_annonce_update_cancel(Event $event)
    {

        return $this->redirectToRoute('app_event_news',['name' => $event->getName()]);

    }


}
