<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Update;


class EventChatController extends AbstractController
{
    #[Route('/event/chat', name: 'app_event_chat')]
    public function chat(): Response
    {
        return $this->render('event_chat/index.html.twig');
    }

    #[Route('/push', name: 'app_event_chat_push')]
    public function push(Request $request, HubInterface $hub)
    {

        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}
