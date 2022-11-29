<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\EventRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class RechercheController extends AbstractController
{
    //Controller ppour gérer la partie recherche

    #[Route('/recherche', name: 'app_recherche', methods: ['get', 'post'])]
    public function recherche(Request $request, EventRepository $eventRepo, UserRepository $userRepo, PaginatorInterface $paginator): Response
    {

        //Je prend les données du formulaire de recherche de la navbar
        $data = $request->query->get('recherche');

        //On recherche dans colonne nom et puis dans la colonne tag
        $allevents = $eventRepo->findBySearch($data);

        //On recherche dans la colonne identifiant, prenom et nom
        $users = $userRepo->findBySearch($data);

        // Paginate the results of the query
        $events = $paginator->paginate(
            // Doctrine Query, not results
            $allevents,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );


        return $this->render('recherche/recherche.html.twig', [
            'events' => $events,
            'users' => $users
        ]);
    }
}