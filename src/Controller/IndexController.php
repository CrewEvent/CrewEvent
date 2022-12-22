<?php

/* 
    -Controller générale pour nos pages version 1.0
    -A séparer aprés par exemple ProfileControleer pour les pages liées au profil etc..
    -Liste des méthodes dans cette version:
    --index : /, app_index
    --profile: /profile, app_profile
    --edit_profile: /profile/edit, app_edit_profile
    --edit_password: /profile/edit_password, app_edit_password

*/

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Form\EditPhotoType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class IndexController extends AbstractController
{
    /* 
    -Render la page d'acceuil version 1. Accés connection, déconnection, visit du profil

    */

    #[Route('/index', name: 'app_index')]
    public function index(UserRepository $userRepo, EventRepository $eventRepo, ParticipantRepository $participantRepo): Response
    {
        // prend tous les utilisateurs dans le repo user
        $users = $userRepo->findAll();

        //On récupére l'utilisateur connécté
        $user = $this->getUser();



        //On fait cette partie si c'est un utilisateur connecté
        if ($user) {

            //prend tous les événements dans le repo event
            $events = $eventRepo->findAll();

            //On prend tous les événements dont l'utilisateur a participé
            //c'est à dire dans la table participant on recherche le username
            $participants = $participantRepo->findBy(['participantUsername' => $user->getUserIdentifier()]);
            return $this->render(
                'pages/index.html.twig',
                [
                    'users' => $users,
                    'events' => $events,
                    'participants' => $participants
                ]
            );
        } else {
            return $this->render('pages/index.html.twig');
        }
    }


}
