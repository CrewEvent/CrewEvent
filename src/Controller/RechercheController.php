<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    //Controller ppour gérer la partie recherche

    #[Route('/recherche', name: 'app_recherche', methods: ['post', 'get'])]
    public function recherche(Request $request): Response
    {

        //Je prend les données du formulaire de recherche de la navbar
        $data = $request->request->all()['recherche'];


        // $events = $eventRepo->findBy(['name' => $data]);

        // $users = $userRepo->findBy(['username' => $data]);

        return $this->render('recherche/recherche.html.twig');
    }
}