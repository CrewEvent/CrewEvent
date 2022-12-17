<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Turbo\TurboBundle;



class EventInfosController extends AbstractController
{
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

        if ($isParticipant == false) {
            $this->addFlash("warning", "Vous n'avez pas encore participé à cet événément");
        }
        //retourne la page
        return $this->render('pages/event/event_show.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant
        ]);
    }
    //Partie Ajout de nouvelles infos générales
    #[Route('/event/info/add/{name}', name: 'event_info_add', methods: ['POST', 'GET'])]
    public function event_info_add(Event $event, Request $request, EntityManagerInterface $em)
    {
        //On récupére d'abord les donnéees
        $data = $request->query->all();


        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event->setInfoGenerale($values);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_add.stream.html.twig', ['event' => $event, 'info' => $values]);

    }

    //Partie suppression de l'info
    #[Route('/event/delete/info/{name}/{info_title}', name: 'event_info_delete', methods: ['POST', 'GET'])]
    public function event_info_delete(Event $event, string $info_title, EntityManagerInterface $em, Request $request)
    {

        //On recherche la clé du titre dans la liste des infos avec arra_search():key
        //On supprime avaec unset(key)

        //On récupére tous les événements
        $infos = $event->getInfoGenerale();

        //On innitialise le compteur
        $i = 0;
        //On parcours la table infos pour chercher title
        foreach ($infos as $elt){
            //Si on trouve on récupére la clé dans res de la table $info
            if(array_search($info_title, $elt )){
                $res = $i;
            }
            $i ++;
        }

        //Puis on supprime et on enregistre
        $event = $event->deleteInfoGenerale($res);
        $em->persist($event);
        $em->flush();


        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_remove.stream.html.twig', ['title' => $info_title, ]);


        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!

    }

    #[Route('/event/update/{name}/{info_title}', name: 'app_event_update_info', methods: ['POST', 'GET'])]
    public function event_info_update(Event $event, string $info_title, Request $request)
    {

        $infos = $event->getInfoGenerale();

        $i = 0;

        foreach ($infos as $elt){
            if(array_search($info_title, $elt )){
                $res = $i;
            }
            $i ++;
        }
        //On récupre d'abord le contenu qui a pour titre info_title
        $info_content = $infos[$res]['content'];

        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_update.stream.html.twig', ['event' => $event,'title' => $info_title, 'content' => $info_content, 'index'=>$res]);

        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!
    }

    #[Route('/event/info/update/success/{name}/{index}', name: 'event_info_update_success', methods: ['POST', 'GET'])]
    public function event_info_update_success(Event $event, int $index, EntityManagerInterface $em, Request $request)
    {
        $data = $request->query->all();

        //On récupére d'abord les donnéees
        $data = $request->query->all();

        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event = $event->updateInfoGenerale($values, $index);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_show_event',['name' => $event->getName()]);

    }

    #[Route('/event//info/update/cancel/{name}', name: 'event_info_update_cancel', methods: ['POST', 'GET'])]
    public function event_info_update_cancel(Event $event)
    {

        return $this->redirectToRoute('app_show_event',['name' => $event->getName()]);

    }
}
