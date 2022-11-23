<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PubController extends AbstractController
{
    #[Route('/pub', name: 'app_pub')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://crewevent/event/chat',
            json_encode(['content' => 'im in love with you'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}