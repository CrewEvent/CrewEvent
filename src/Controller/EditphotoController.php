<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditphotoController extends AbstractController
{
    #[Route('/editphoto', name: 'app_editphoto')]
    public function index(): Response
    {
        return $this->render('editphoto/index.html.twig', [
            'controller_name' => 'EditphotoController',
        ]);
    }
}   
