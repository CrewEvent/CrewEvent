<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Turbo\TurboBundle;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_comment')]
    public function comment(Request $request, Publication $publication, EntityManagerInterface $em): Response
    {

        //On récupére le commentaire
        $data = $request->query->get('comment');
        $user = $this->getUser()->getUserIdentifier();
        //Le commentaire et le commentateur dans la table publication
        $publication->setComments(['comment' => $data, 'commenter' => $user]);

        //On met dans la base de donnée
        $em->persist($publication);
        $em->flush();


        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('comment/comment.stream.html.twig', ['id' => $publication->getId(), 'comment' => $data, 'commenter' => $user]);


        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!
    }
}