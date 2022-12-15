<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Participant;
use App\Form\EventCreationType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Turbo\TurboBundle;

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
    #[Route('/event/show/{name}', name: 'app_show_event', methods: ['POST', 'GET'])]
    public function event_show(Event $event, ParticipantRepository $participantRepo): Response
    {

        //On récupére l'identifiant de l'utilisateur connecté
        $username = $this->getUser()->getUserIdentifier();

        //Cherche si l'utilisateur est déja participant
        // $isParticipant = $this->search_if_participant($username, $event->getName());

        $isParticipant = false;

        //On prend tous les objets participants qui ont pour attribut le nom de l'événement
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);

        //Pour chque objet on regarde si le nom d'utilisateur est celui de l'utilisateur connecté
        foreach ($participants as $participant) {
            if ($participant->getParticipantUsername() == $username) {
                $isParticipant = true;
            }
        }

        if ($isParticipant == false) {
            $this->addFlash("warning", "Vous n'avez pas encore participé à cet événément");
        }
        //retourne la page
        return $this->render('pages/event/event_show.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'isParticipant' => $isParticipant
        ]);
    }

    //Partie annonces de l'événement
    #[Route('/event/show/{name}/news', name: 'app_event_news', methods: ['POST', 'GET'])]
    public function event_news(Event $event, ParticipantRepository $participantRepo): Response
    {
        $participants = $participantRepo->findBy(['eventName' => $event->getName()]);
        return $this->render('evenement/news.html.twig', [
            'event' => $event,
            'participants' => $participants
        ]);
    }

    //Partie Ajout de nouvelles infos générales
    #[Route('/event/show/{name}/add_infos', name: 'app_event_infos', methods: ['POST', 'GET'])]
    public function event_add_infos(Event $event, Request $request, EntityManagerInterface $em)
    {
        //On récupére d'abord les donnéees
        $data = $request->query->all();


        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event->setInfoGenerale($values);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_add.stream.html.twig', ['id' => $event->getId(),'event' => $event, 'info' => $values]);


        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!
    }

    //Partie suppression de l'info
    #[Route('/event/delete/{name}/{info_title}', name: 'app_event_delete_info', methods: ['POST', 'GET'])]
    public function event_info_delete(Event $event, string $info_title, EntityManagerInterface $em, Request $request)
    {
        //On recherche la clé du titre dans la liste des infos avec arra_search():key
        //On supprime avaec unset(key)

        //On récupére tous les événements
        $infos = $event->getInfoGenerale();

        //On innitialise le compteur
        $i = 0;
        //On parcours la table infos pour chercher title
        foreach ($infos as $elt){
            //Si on trouve on récupére la clé dans res de la table $info
            if(array_search($info_title, $elt )){
                $res = $i;
            }
            $i ++;
        }

        //Puis on supprime et on enregistre
        $event = $event->deleteInfoGenerale($res);
        $em->persist($event);
        $em->flush();


        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_remove.stream.html.twig', ['title' => $info_title, ]);


        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!

    }

    #[Route('/event/update/{name}/{info_title}', name: 'app_event_update_info', methods: ['POST', 'GET'])]
    public function event_info_update(Event $event, string $info_title, Request $request)
    {



        $infos = $event->getInfoGenerale();

        $i = 0;

        foreach ($infos as $elt){
            if(array_search($info_title, $elt )){
                $res = $i;
            }
            $i ++;
        }
        //On récupre d'abord le contenu qui a pour titre info_title
        $info_content = $infos[$res]['content'];

        // 🔥 The magic happens here! 🔥

        // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('info_generale/info_generale_update.stream.html.twig', ['event' => $event,'title' => $info_title, 'content' => $info_content, 'index'=>$res]);

        // If the client doesn't support JavaScript, or isn't using Turbo, the form still works as usual.
        // Symfony UX Turbo is all about progressively enhancing your apps!
    }

    #[Route('/event//info/update/success/{name}/{index}', name: 'event_info_update_success', methods: ['POST', 'GET'])]
    public function event_info_update_success(Event $event, int $index, EntityManagerInterface $em, Request $request)
    {
        $data = $request->query->all();

        //On récupére d'abord les donnéees
        $data = $request->query->all();

        $values = ['title' => $data['title'] ,'content' => $data['content']];
        $event = $event->updateInfoGenerale($values, $index);

        //On enregistre dans la base de donnée
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_show_event',['name' => $event->getName()]);

    }

    #[Route('/event//info/update/cancel/{name}', name: 'event_info_update_cancel', methods: ['POST', 'GET'])]
    public function event_info_update_cancel(Event $event)
    {

        return $this->redirectToRoute('app_show_event',['name' => $event->getName()]);

    }

}