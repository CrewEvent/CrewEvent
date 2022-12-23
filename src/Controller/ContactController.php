<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Contact;
use App\Form\FindContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


use function PHPUnit\Framework\isNull;

class ContactController extends AbstractController
{
    #[Route('/contact/add/{username}', name: 'add_contact')]
    public function add_contact(User $user, EntityManagerInterface $em)
    {
        //Créer un nouveau contact
        $contact = new Contact;

        //On définit la clé étrangére
        $contact->setUser($user);

        //On prend l'identifiant dde l'utilisateur connecté
        $contact->setUsername($this->getUser()->getUserIdentifier());
        $contact->setContactUsername($user->getUserIdentifier());

        //On met dans la base de donnée
        $em->persist($contact);
        $em->flush($contact);

        //puis on redirigige dans la page d'acceuil ?? à revoir
        //rajout la redirection vers show_profile
        return $this->redirectToRoute('app_show_profile',['username' =>$user->getUserIdentifier()]);
    }

    #[Route('/contact/remove/{username}', name: 'remove_contact')]
    public function remove_contact(User $user, ContactRepository $contactRepo, EntityManagerInterface $em)
    {
        $contact = $contactRepo->findOneBy(['user' => $user->getId(), 'username' => $this->getUser()->getUserIdentifier()]);

        //On retire le contact de la base de donnée_
        $em->remove($contact);
        $em->flush($contact);

        //puis on redirigige dans la page d'acceuil ?? à revoir
        //rajout la redirection vers show_profile
        
        return $this->redirectToRoute('app_show_profile', ['username' =>$user->getUserIdentifier()]);
    }

    // Recherche de contacts
    #[Route('/contact', name: 'app_contacts')]
    public function show_contacts(ContactRepository $contactRepo, Request $request, EntityManagerInterface $em, $ordered_contacts = [])
    {
        $form = $this->createForm(FindContactType::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);
        $contacts = $contactRepo->findBy(['username' => $this->getUser()->getUserIdentifier()]);

        if (!isset($ordered_contacts) || isNull($ordered_contacts) || count($ordered_contacts) == 0) {
            $ordered_contacts = [];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $ordered_contacts = array();

            for ($index = 0, $count = count($contacts); $index < $count; $index++) {
                // On trie les contacts du par proximité par rapport à la recherche
                array_push($ordered_contacts, (string)$contacts[$index]->getContactUsername());
                levenshtein(($ordered_contacts[$index]), $form->getData()->getContactUsername()) < levenshtein($ordered_contacts[0], $form->getData()->getContactUsername()) ?  [$ordered_contacts[$index], $ordered_contacts[0]] = [$ordered_contacts[0], $ordered_contacts[$index]] : null;
            }

            return $this->render('contact/show_contacts.html.twig', ['contacts' => $contacts, 'FindContactType' => $form->createView(), 'ordered_contacts' => $ordered_contacts]);
        }

        return $this->renderForm('contact/show_contacts.html.twig', ['contacts' => $contacts, 'FindContactType' => $form, 'ordered_contacts' => $ordered_contacts]);
    }
}
