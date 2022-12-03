<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Contact;
use App\Form\FindContactType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    #[Route('/contact/add/{username}', name: 'add_contact')]
    public function add_contact(User $user, EntityManagerInterface $em)
    {


        // if ($contactRepo->findBy(['username' => $user->getUserIdentifier()])) {

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
        return $this->redirectToRoute('app_index');
    }

    #[Route('/contact/remove/{username}', name: 'remove_contact')]
    public function remove_contact(User $user, ContactRepository $contactRepo, EntityManagerInterface $em)
    {
        $contact = $contactRepo->findOneBy(['user' => $user->getId(), 'username' => $this->getUser()->getUserIdentifier()]);

        //On retire le contact de la base de donnée
        $em->remove($contact);
        $em->flush($contact);

        //puis on redirigige dans la page d'acceuil ?? à revoir
        return $this->redirectToRoute('app_index');
    }

    // Recherche de contacts
    #[Route('/contact', name: 'app_contacts')]
    public function show_contacts(ContactRepository $contactRepo, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(FindContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $em->createQuery(
                'SELECT contact FROM App\Entity\Contact contact WHERE contact.username = :username'
            )
                ->setParameter('username', $this->getUser()->getUserIdentifier());
            $contacts = $query->getResult();
            $ordered_contacts = array();

            for ($index = 0, $count = count($contacts); $index < $count; $index++) {
                // On trie les contacts du par proximité par rapport à la recherche
                array_push($ordered_contacts, (string)$contacts[$index]->getContactUsername());
                levenshtein(($ordered_contacts[$index]), $form->getData()->getContactUsername()) < levenshtein($ordered_contacts[0], $form->getData()->getContactUsername()) ?  [$ordered_contacts[$index], $ordered_contacts[0]] = [$ordered_contacts[0], $ordered_contacts[$index]] : null;
            }
            $contacts = $ordered_contacts; // Le tableau retourné est la liste des contacts partant du plus proche de la chaine recherchée
        } else {
            $contacts = $contactRepo->findBy(['username' => $this->getUser()->getUserIdentifier()]);
            $ordered_contacts = $contacts;
        }

        return $this->render('contact/show_contacts.html.twig', ['contacts' => $contacts, 'FindContactType' => $form->createView(), 'ordered_contacts' => $ordered_contacts]);
    }
}
