<?php

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


    #[Route('/profile/edit', name: 'app_edit_profile', methods: ['POST', 'GET'])]
    public function edit_profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            return $this->redirectToRoute('app_profile');
        }

        return $this->render(
            'pages/edit_profile.html.twig',
            ['form' => $form->createView()]

        );
    }

    #[Route('/profile/edit_password', name: 'app_change_password', methods: ['POST', 'GET'])]
    public function edit_password(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )

            );

            $em->flush();
            return $this->redirectToRoute('app_profile');
        }


        return $this->render(
            'pages/app_change_password.html.twig',
            ['form' => $form->createView()]

        );
    }
}