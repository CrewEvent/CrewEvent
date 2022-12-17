<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditphotoController extends AbstractController
{
    #[Route('/editphoto', name: 'profile_editphoto')]
    public function index(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $username = $user->getUsername();

        /** @var UploadedFile $file */
        $file = $request->files->get(key: 'edit_photo')['photoProfile'];
        if ($file) {
            $filename = 'profile_' .  $username . '.' .  $file->guessExtension();
            $file->move($this->getParameter(name: 'uploads_dir'), $filename);
            $user->setPhotoProfile($filename);
            $em->persist($user);
            $em->flush();
        }
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/delete', name: 'profile_deletephoto')]
    public function remove(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $user->setPhotoProfile(null);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('app_profile');    
    }
}


