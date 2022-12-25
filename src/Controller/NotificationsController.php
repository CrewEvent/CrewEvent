<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationsController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function notifications(NotificationRepository $notifRepo): Response
    {
        $notifications = $notifRepo->findBy(['user' => $this->getUser()->getId()],['date' =>'DESC']);

        return $this->render('notifications/notifications.html.twig',[
            'notifications' => $notifications
            ]
        );
    }
}

