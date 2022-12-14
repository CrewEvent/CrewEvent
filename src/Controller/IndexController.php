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
    -Render la page d'acceuil version 0. Accés connection, déconnection, visit du profil

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

    /* 
    -Render la page de profil
    */

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {

        $user = $this->getUser();
        $photoProfile = $user->getphotoProfile();
        $form = $this->createForm(EditPhotoType::class, null, [
            'action' => $this->generateUrl('profile_editphoto'),
            'method' => 'POST',
            'attr' => [
                'class' => 'edit_img_form'
            ]
        ]);

        dump($photoProfile);

        return $this->render(
            'pages/profile.html.twig',
            [
                'Form' => $form->createView(),
                'photoProfile' => $photoProfile ? 'images/' . $photoProfile : 'images/pas_de_photo.png'
            ]
        );
    }


    /* 
    -Edition du profil de l'utilisateur connecté et ou ajout d'informations supplémentaires
    -On envoie dans la base de donnée aprés soumission du formulaire si elle est valide bien sur
    */

    #[Route('/profile/edit', name: 'app_edit_profile', methods: ['POST', 'GET'])]
    public function edit_profile(Request $request, EntityManagerInterface $em): Response
    {
        //on récupére les informations de l'utilisateur conrant qu'on stocke dans $user 
        $user = $this->getUser();
        //On crée une formulaire de type UserType
        $form = $this->createForm(UserType::class, $user);

        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Si le formulaire est envoyé et validé on stocke les informations rentrées dans la base de donnée
            $em->flush();
            //puis on redirrive l'utilisateur dans la page de profil
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm(
            'pages/edit_profile.html.twig',
            ['form' => $form]

        );
    }


    /* 
    -Page d'édition du mot de passe
    -On envoie dans la base de donnée aprés soumission du formulaire si elle est valide bien sur
    */

    #[Route('/profile/edit_password', name: 'app_change_password', methods: ['POST', 'GET'])]
    public function edit_password(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        //on récupére les informations de l'utilisateur conrant qu'on stocke dans $user 
        $user = $this->getUser();
        //On crée une formulaire de type Change passwordType
        $form = $this->createForm(ChangePasswordType::class, $user);

        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //On hash le mot de passe rentré dans le formulaire
            //En vrai hashPassword() prend le mot de passe en plain et le sel cryptologique qui est propre à l'utilisateur
            //Pour générer le nouveau mot de passe que l'on va stocké dans la base de donnée
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )

            );
            //Envoie dans la base de donnée
            $em->flush();
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm(
            'pages/app_change_password.html.twig',
            ['form' => $form]

        );
    }

    //Affiche la page de profil d'un utilisateur en mode view only pas d'édition
    //En attribut si on indique username dans la route et que l'on injecte User
    //symfony sait que l'entité que l'on va utiliser est la méme que celui de la route
    #[Route('/show_profile/{username}', name: 'app_show_profile')]
    public function show_profile(ContactRepository $contactRepo, User $user): Response
    {
        //On regarde d'abord si cet utilisateur est déja contact
        $isContact = false;


        //s'il se trouve dans la liste on enléve le bouton ajouter
        if ($contactRepo->findBy(['user' => $user->getId()])) {

            $isContact = true;
        }

        return $this->render('pages/show_profile.html.twig', ['user' => $user, 'isContact' => $isContact]);
    }
}
