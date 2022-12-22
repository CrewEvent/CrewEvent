<?php
/* 
    -Gére la partie sécurité c'est à dire la partie Connection de l'utilisateur dans l'application
    -liste des méthodes:
    --securityController: /login, app_login
    --logout: /logout, app_logout

    */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/*
--Page de conncetion de l'utilisateur
*/

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $this->addFlash(
                'error',
                'Identifiants incorrects !'
            );
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /*
    --Page de déconnection de l'utilisateur
*/

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->render('landing_page/landing_page.html.twig');
    }
}
