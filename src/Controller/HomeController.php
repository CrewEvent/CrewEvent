<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\PublicationController;
use App\Repository\CommentRepository;
use App\Repository\PublicationRepository;

class HomeController extends PublicationController
{
    #[Route('/home', name: 'app_home')]
    public function home(PublicationRepository $postRepo,
                         EventRepository $eventRepo,
                         ParticipantRepository $participantRepo
                                                                ): Response
    {
        $participants = $participantRepo->findBy(['participantUsername' => $this->getUser()->getUserIdentifier()]);

        //On prend rous les post
        $post = $postRepo->findAll();


        //Je fais ici la suggestion d'événements
        //Je récupére la liste des tags des événements participés par l'utilisateurs dans une liste
        $tags = [];
        foreach ($participants as $participant){
            array_push($tags, $participant->getEvent()->getTag());
        }

        //Je décompte chaque tag
        $tags = array_count_values($tags);

        //Je fais le trie
        arsort($tags);

        //Je prend les 2 premiers tags dans une list des tags
        $fav = array_keys(array_slice($tags, 0,2));

        //On récupére les événements qui ont pour tag ces tags favories
        $favs = $eventRepo->findfavEvent($fav);

        $events = [];
        //On filtre les événements qui on t plus de participants
        foreach ($favs as $event){
            array_push($events, [$event->getName(), $event->getParticipants()->count()]);
        }

        //On trie la liste des événements
       /* foreach ($events as $event){
            $min_idx =
        }
        dd($events);*/


        return $this->render('home/home.html.twig', [
            'post' => $post,
            'participants'=>$participants
        ]);
    }
}