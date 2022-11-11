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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ChangePasswordType;

class IndexController extends AbstractController
{
    /* 
    -Render la page d'acceuil version 0. Accés connection, déconnection, visit du profil

    */

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /* 
    -Render la page de profil
    */

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {

        return $this->render(
            'pages/profile.html.twig'
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

        return $this->render(
            'pages/edit_profile.html.twig',
            ['form' => $form->createView()]

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

        return $this->render(
            'pages/app_change_password.html.twig',
            ['form' => $form->createView()]

        );
    }
}