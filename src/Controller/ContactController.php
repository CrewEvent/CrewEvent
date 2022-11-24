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

    #[Route('/contact', name: 'app_contacts')]
    public function show_contacts(ContactRepository $contactRepo, Request $request, EntityManagerInterface $em)
    {
        $contact = new Contact;
        $contacts = $contactRepo->findBy(['username' => $this->getUser()->getUserIdentifier()]);

        $form = $this->createForm(FindContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $em->createQuery(
                'SELECT contact FROM App\Entity\Contact contact WHERE contact.username = :username AND contact.contactUsername LIKE :contact_username'
            )
                ->setParameter('username', $this->getUser()->getUserIdentifier())
                ->setParameter('contact_username', '%' . $form->getData()->getContactUsername() . '%');

            $contacts = $query->getResult();
        }


        return $this->render('contact/show_contacts.html.twig', ['contacts' => $contacts, 'FindContactType' => $form->createView()]);
    }
}
