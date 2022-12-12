<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_landing_page')]
    public function landing_page(): Response
    {
        return $this->render('landing_page/landing_page.html.twig');
    }
}