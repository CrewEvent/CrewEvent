<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render(
            'pages/profile.html.twig'
        );
    }
    #[Route('/profile/edit', name: 'app_edit_profile')]
    public function edit_profile(): Response
    {
        return $this->render(
            'pages/edit_profile.html.twig'
        );
    }
}