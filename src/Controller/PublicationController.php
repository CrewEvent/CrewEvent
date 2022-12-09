<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\Turbo\TurboBundle;

class PublicationController extends AbstractController
{

    // Ce controller gére la nouvelle publication
    #[Route('/new_post', name: 'app_new_post', methods: ['get', 'post'])]
    public function new_post(Request $request, EntityManagerInterface $em)
    {
        //Je prend les données du formulaire de recherche de la navbar
        $data = $request->query->get('post');

        //On crée une nouvelle publication
        $publication = new Publication;

        //On définit l'identifiant du publieur dans la bdd
        $publication->setPublisher($this->getUser()->getUserIdentifier());
        $publication->setContent($data);
        //On persist et flush dans la bdd
        $em->persist($publication);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/like/{id}', name: 'post_like', methods: ['get', 'post'])]
    public function like(Publication $publication, EntityManagerInterface $em, Request $request)
    {
        //On récupére l'utilisateur connecté
        $user = $this->getUser()->getUserIdentifier();
        $liked = false;

        //On commence à chercher s'il y'a des likes

        //On cherche si l'utilisateur a déja liké le post
        foreach ($publication->getLikes() as $liker) {
            if ($liker['liker'] == $user) {
                $liked = true;
                $publication = $publication->unlike($user);
            }
        }


        if (!$liked) {
            //On définit l'utilisateur qui a liké dans la bdd
            $publication->setLikes(['liker' => $user]);
        }



        //On enregistre dans la bdd
        $em->persist($publication);
        $em->flush();

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('like/like.stream.html.twig', ['publication' => $publication]);
    }
}