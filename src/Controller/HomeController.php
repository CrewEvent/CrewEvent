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
    public function home(PublicationRepository $postRepo, EventRepository $eventRepo, ParticipantRepository $participantRepo): Response
    {
        $participants = $participantRepo->findBy(['participantUsername' => $this->getUser()->getUserIdentifier()]);

        //On prend rous les post
        $post = $postRepo->findAll();

        return $this->render('home/home.html.twig', [
            'post' => $post,
            'participants'=>$participants
        ]);
    }
}