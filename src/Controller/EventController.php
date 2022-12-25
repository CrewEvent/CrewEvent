<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Publication;
use App\Form\EventCreationType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/*
Controller pour la gestion d'événement
*/

class EventController extends AbstractController
{
    #[Route('/event/creation', name: 'app_event_creation', methods: ['POST', 'GET'])]
    public function event_creation(Request $request, EntityManagerInterface $em): Response
    {
        //on récupére les infos de l'utilisateur
        $user = $this->getUser();

        //On crée un nouveau événement
        $event = new Event;

        //On crée un nouveau formulaire de création d'événement
        $form = $this->createForm(EventCreationType::class, $event);


        //on dit au formulaire de gérer les requettes
        $form->handleRequest($request);

        //Si le formulaire est soumis mais pas valide
        if ($form->isSubmitted() && !$form->isValid()) {


            $this->addFlash('warning', 'Vérifier que les éléments soient bien renseignés');
            return $this->redirectToRoute('app_event_creation');
        }
        //Si le formulaire est soumis et valid

        if ($form->isSubmitted() && $form->isValid()) {

            //Donner un utilisateur à l'événement
            $event->setUser($user);
            //Enregistrement dans la base de donnée
            $em->persist($event);
            $em->flush();
            //redirection vers la page de l'événement
            //on récupére le nom dans le formulaire qu'on donne en paramétre à app_show_event
            return $this->redirectToRoute('app_add_participant', ['name' => $form->get('name')->getData()]);
        }



        //ça affiche page de création d'événement
        return $this->render('pages/event/event_creation.html.twig', [
            'form' => $form->createView()
        ]);
    }


    //Page de l'événement
    #[Route('/event/update/{name}', name: 'app_event_update', methods: ['POST', 'GET'])]
    public function event_update(Event $event, Request $request, ParticipantRepository $participantRepo, EntityManagerInterface $em)
    {
        //On prend tous les objets participants qui ont pour attribut le nom de l'événement
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);

        //On crée un nouveau formulaire de création d'événement
        $form = $this->createForm(EventCreationType::class, $event);

        //on dit au formulaire de gérer les requettes
        $form->handleRequest($request);

        //Si le formulaire est soumis mais pas valide
        if ($form->isSubmitted() && !$form->isValid()) {


            $this->addFlash('warning', 'Vérifier que les éléments soient bien renseignés');

            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('error_layouts/event_modification.stream.html.twig');
        }
        //Si le formulaire est soumis et valid

        if ($form->isSubmitted() && $form->isValid()) {

            //On set le nouveau non de l'événement dans la liste des membres
            foreach ($participants as $participant){
                $participant->setEventName($form->get('name')->getData());
                $em->persist($participant);
            }
            //Enregistrement dans la base de donnée
            $em->persist($event);
            $em->flush();

            //On update la liste des membres de l'événement, on change juste le nom
            return $this->redirectToRoute('app_show_event', ['name' => $event->getName()]);

        }


        //ça affiche page de création d'événement
        return $this->render('pages/event/event_modification.html.twig', [
            'event' =>$event,
            'form' => $form->createView(),
            'participants' => $participants,
        ]);
    }

    //Page de l'événement
    #[Route('/event/deletion/{name}', name: 'event_deletion', methods: ['POST', 'GET'])]
    public function event_deletion(Event $event,EventRepository $eventRepo)
    {
        $eventRepo->remove($event,true);
        return $this->redirectToRoute('app_home');
    }

    //Page de l'événement
    #[Route('/event/participant/quit/{participantUsername}', name: 'event_participant_quit', methods: ['POST', 'GET'])]
    public function event_participant_quit(Participant $participant, EntityManagerInterface $em)
    {

        $event = $participant->getEvent();
        $event->removeParticipant($participant);
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('app_home');
    }

    //My events
    #[Route('/my_events', name: 'app_my_events', methods: ['POST', 'GET'])]
    public function my_events(ParticipantRepository $participantRepo,EventRepository $eventRepo,EntityManagerInterface $em):response
    {
        //On regarde dans la liste des événements pour voir les événements qu'on a crée
        //On retourne donc un tableau des événements que j'ai crée
        $created = $eventRepo->findBy(['user'=> $this->getUser()->getId()]);

        //On regarde la liste des participants
        //On retourne donc la liste des événments que j'ai participés
        $participated = $participantRepo->findBy(['participantUsername'=> $this->getUser()->getUserIdentifier()]);

        return $this->render('pages/event/my_events.html.twig',[
            'events_created'=>$created,
            'events_participated'=>$participated
        ]);
    }
    #[Route('image/modification/{name}', name: 'event_image_modification', methods: ['POST', 'GET'])]
    public function image_modification(Event $event, Request $request,EntityManagerInterface $em){
        $file = $request->files->get('image');

        if ($file instanceof UploadedFile && $file->isValid()) {
            // Récupération du nom original du fichier
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // Génération d'un nom de fichier unique
            $newFilename = uniqid().'.'.$file->guessExtension();

            // Déplacement du fichier téléchargé dans le répertoire de destination
            $file->move(
                $this->getParameter('uploads_dir'),
                $newFilename
            );


            // Mise à jour de l'entité avec le nouveau nom de fichier
            $event->setImage($newFilename);
            $em->persist($event);
            $em->flush();

           return $this->redirectToRoute('app_show_event',['name' => $event->getName()]);


        }
    }
    //Partage de l'événement

    #[Route('event/share/{name}', name: 'event_share', methods: ['POST', 'GET'])]
    public function event_share(Event $event, EntityManagerInterface $em,RouterInterface $router)
    {
        //On crée une nouvelle pulication
        $post = new Publication;

        //ON définit l'utilisateur qui fait le post
        $post->setPublisher($this->getUser()->getUserIdentifier());

        //On récupre l'url de l'événement
        $url = $router->generate('app_show_event', ['name'=>$event->getName()]);

        //On le stransforme en string pour l'enregistrer dans la base de donnée
        $link = (string)$url;

        //On définit le contenu du post
        $post->setContent("Si vous étes fan de ".$event->getTag().", venez découvrir ".$event->getName());
        //Le lien vers l'événement
        $post->setLink($link);

        //Enregistrement
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

}