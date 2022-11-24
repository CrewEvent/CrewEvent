
        $contacts = $contactRepo->findBy(['username' => $this->getUser()->getUserIdentifier()]);
        return $this->render('contact/show_contacts.html.twig', ['contacts' => $contacts]);
    }
}