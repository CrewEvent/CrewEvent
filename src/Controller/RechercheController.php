<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\EventRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    //Controller ppour gérer la partie recherche

    #[Route('/recherche', name: 'app_recherche', methods: ['get', 'post'])]
    public function recherche(Request $request, EventRepository $eventRepo, UserRepository $userRepo): Response
    {

        //Je prend les données du formulaire de recherche de la navbar
        $data = $request->query->get('recherche');


        $events = $eventRepo->findBy(['name' => $data]);

        $users = $userRepo->findBy(['username' => $data]);

        return $this->render('recherche/recherche.html.twig', [
            'events' => $events,
            'users' => $users
        ]);
    }
}