<?php

namespace App\Controller;

use App\Entity\PrivateMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use App\Repository\PrivateMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Member;
use App\Repository\MemberRepository;

//Controller pour la messagerie privee
class PrivateMessageController extends AbstractController
{
    //On rentre dans la page des messages privées
    #[Route('/private/message', name: 'private_message')]
    public function private_message(MemberRepository $memberRepo): Response
    {
        //On va afficher les membres et le nouveau message avec qui l'utilisateur a fait un tchat
        $members = $memberRepo->findMessages($this->getUser()->getUserIdentifier());


        //Puis on render la page
        return $this->render('private_message/private_message.html.twig', ['members' => $members]);
    }


    //Pour envoyer un message privée à un utilisateur
    #[Route('/private/message/{username}', name: 'send_private_message')]
    public function send_message(Request $request, HubInterface $hub, User $user, EntityManagerInterface $em, PrivateMessageRepository $messageRepo, MemberRepository $memberRepo): Response
    {



        //On affiche d'abord les messages entre ces 2 utilisateurs
        //cad On récupére les messages envoyés ou reçu par l'utilisateur
        $messages = $messageRepo->findExchange($this->getUser()->getUserIdentifier(), $user->getUserIdentifier());


        //On crée un nouveau message
        $message = new PrivateMessage;

        //On crée le formulaire de message
        $form = $this->createFormBuilder()
            ->add(
                'message',
                TextType::class,
                ['attr' => ['autocomplete' => 'off', 'class' => 'form-control form-control-lg', 'placeholder' => 'entrer un message']]
            )

            ->getForm();

        //On le clone juste pour pouvoir le réutiliser
        $emptyForm = clone $form;

        //On cherche si l'utilisateur connecté a passé un tchat avec autre urilisateur
        $member = $memberRepo->findExchange($this->getUser()->getUserIdentifier(), $user->getUserIdentifier());

        //Si il n'existe pas on la crée
        if ($messages == null) {
            $member = new Member;
            $member->setUser($this->getUser());
        }

        //C'est le canal ou ces 2 utilisateurs vont discuter
        $canal = strval($member->getId());
        // dd($member);
        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);

        //Si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            //On prend les données du formulaire cad le contenu du message
            $data = $form->getData();

            //On définit la clé étrangére
            $message->setUser($this->getUser());

            //On déinit l'envoyeur
            $message->setSender($this->getUser()->getUserIdentifier());

            //On définit le destinataire
            $message->setReceiver($user->getUserIdentifier());

            //On définit le contenu du message
            $message->setContent($data['message']);


            //On enregistre le mebre dans la base de donnée
            $member->setUsername($this->getUser()->getUserIdentifier());
            $member->setOtherUsername($user->getUserIdentifier());
            $member->setLastMessage($data['message']);



            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure

            $hub->publish(new Update(
                $canal,
                $this->renderView('chat/private_message.stream.html.twig', ['message' => $data['message'], 'receiver' => $user->getUserIdentifier()])
            ));


            //On registre maintenant dans la base de donnée
            $em->persist($message);
            $em->persist($member);
            $em->flush();
        }
        // Force an empty form to be rendered below
        // It will replace the content of the Turbo Frame after a post
        $form = $emptyForm;
        return $this->renderForm('private_message/send_private_message.html.twig', ['form' => $form, 'user' => $user, 'messages' => $messages, 'canal' => $canal]);
    }
}