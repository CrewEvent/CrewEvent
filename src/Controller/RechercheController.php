<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class RechercheController extends AbstractController
{
    //Controller ppour gérer la partie recherche

    #[Route('/recherche/process', name: 'app_recherche', methods: ['get', 'post'])]
    public function recherche(Request $request, EventRepository $eventRepo, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {

        $users = [];
        $events = [];

        /// On recupere les infos de l'utilisateur courant
        $user = $this->getUser();

        //On prend les données du formulaire de recherche de la navbar
        $pattern = $request->query->get('recherche');
        $patterns = explode(' ', $pattern);

        //        Recherche d'utilisateurs
        //        On recherche dans la colonne identifiant, prenom et nom
        $allUsers = $userRepository->findAll();

        // On filtre pour avoir que les contacts qui contiennent un des mots recherchés
        $usersResult = [];
        foreach ($allUsers as $user) {
            foreach ($patterns as $value) {
                if (str_contains(strtolower($user->getUserIdentifier()), strtolower($value))) {
                    $usersResult[] = $user;
                }
            }
        }

        $sortedUsers = [];
        foreach ($usersResult as $user) {
            $size = sizeof($sortedUsers);
            $key = levenshtein($pattern, $user->getUserIdentifier());
            $sortedUsers[$key] = $user;
        }
        ksort($sortedUsers);
        /// /// // Fin de la partie recherche utilisateurs

        /// /// // Debut Recherche evenements

        //On recherche dans colonne nom et puis dans la colonne tag
        $allevents = $eventRepo->findAll();

        // On filtre pour avoir que les contacts qui contiennent un des mots recherchés
        $eventsResult = [];
        foreach ($allevents as $event) {
            foreach ($patterns as $value) {
                if (str_contains(strtolower($event->getName()), strtolower($value))) {
                    $eventsResult[] = $event;
                }
            }
        }

        $sortedEvents = [];
        foreach ($eventsResult as $event) {
            $size = sizeof($sortedEvents);
            $key = levenshtein($pattern, $event->getName());
            $sortedEvents[$key] = $event;
        }
        ksort($sortedEvents);
        /// /// // Fin de la partie recherche Evenements


        dump($sortedUsers);
        dump($sortedEvents);


        // Paginate the results of the query
        $events = $paginator->paginate(
        // Doctrine Query, not results
            $sortedEvents,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('recherche/recherche.html.twig', [
            'events' => $events,
            'users' => $sortedUsers
        ]);
    }

    #[Route('/recherche', name: 'render_result')]
    public function render_result(Request $request): Response
    {
        $users = $request->query->get('users');
        $events = $request->query->get('events');


        dump($request, $request->query->get('users'));

        return $this->render('recherche/recherche.html.twig', [
            'events' => $events,
            'users' => $users
        ]);
    }

}
