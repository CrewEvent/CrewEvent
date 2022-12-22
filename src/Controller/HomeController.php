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
    public function home(
        PublicationRepository $postRepo,
        EventRepository       $eventRepo,
        ParticipantRepository $participantRepo
    ): Response {
        $participants = $participantRepo->findBy(['participantUsername' => $this->getUser()->getUserIdentifier()]);

        //On prend rous les post
        $post = $postRepo->findAll();


        //Je fais ici la suggestion d'événements
        //Je récupére la liste des tags des événements participés par l'utilisateurs dans une liste
        $tags = [];
        foreach ($participants as $participant) {
            array_push($tags, $participant->getEvent()->getTag());
        }

        //Je décompte chaque tag
        $tags = array_count_values($tags);

        //Je fais le trie
        arsort($tags);

        //Je prend les 2 premiers tags dans une list des tags
        $fav = array_keys(array_slice($tags, 0, 2));

        //On récupére les événements qui ont pour tag ces tags favories
        $favs = $eventRepo->findfavEvent($fav);


        $events = [];
        //On filtre les événements qui on t plus de participants
        foreach ($favs as $event) {

            array_push($events, [$event->getName(), $event->getParticipants()->count()]);
        }


        //On trie la liste des événements du plus petit nbre de participants au plus grand
        $events = $this->selection_sort($events);


        //On prend les 3 derniers
        $suggestions = array_slice($events, -3, 3);

        //S'il a déja participé on ne va pas lui suggérer encore


        foreach ($participants as $participant) {
            foreach ($suggestions as $key => $suggestion) {
                if ($suggestion[0] == $participant->getEventName()) {
                    unset($suggestions[$key]);
                    //On réindex les valeurs
                    $suggestions = array_values($suggestions);
                }
            }
        }

        return $this->render('home/home.html.twig', [
            'post' => $post,
            'participants' => $participants,
            'suggestions' => $suggestions
        ]);
    }

    function swap_positions($data1, $left, $right)
    {
        $backup_old_data_right_value = $data1[$right];
        $data1[$right] = $data1[$left];
        $data1[$left] = $backup_old_data_right_value;
        return $data1;
    }

    function selection_sort($data)
    {
        for ($i = 0; $i < count($data) - 1; $i++) {
            $min = $i;
            for ($j = $i + 1; $j < count($data); $j++) {

                if ($data[$j][1] < $data[$min][1]) {
                    $min = $j;
                }
            }
            $data = $this->swap_positions($data, $i, $min);
        }
        return $data;
    }
}
