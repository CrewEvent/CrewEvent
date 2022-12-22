<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EditPhotoType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\UX\Turbo\TurboBundle;
use App\Form\UserType;

class ProfileController extends AbstractController
{
    private $user_form;


    /*
 -Render la page de profil
 */

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {


        $user = $this->getUser();
        $photoProfile = $user->getphotoProfile();
        $form = $this->createForm(EditPhotoType::class, null, [
            'action' => $this->generateUrl('profile_editphoto'),
            'method' => 'POST',
            'attr' => [
                'class' => 'edit_img_form'
            ]
        ]);

        dump($photoProfile);

        return $this->render(
            'pages/profile.html.twig',
            [
                'Form' => $form->createView(),
                'photoProfile' => $photoProfile ? 'images/' . $photoProfile : 'images/No_image.jpg'
            ]
        );
    }


    /*
    -Edition du profil de l'utilisateur connecté et ou ajout d'informations supplémentaires
    -On envoie dans la base de donnée aprés soumission du formulaire si elle est valide bien sur
    */

    #[Route('/profile/edit', name: 'app_edit_profile', methods: ['POST', 'GET'])]
    public function edit_profile(Request $request)
    {
        //on récupére les informations de l'utilisateur conrant qu'on stocke dans $user
        $user = $this->getUser();
        //On crée un nouveau formulaire

        $form = $this->createForm(UserType::class,$user,[
            'method'=>'POST',
            'action' => $this->generateUrl('profile_edit_success')
        ]);

        //On modifie sur place
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('profile/profile_edit.stream.html.twig', ['form' => $form->createView()]);
    }

    //Si la modification des données est soumis
    #[Route('/profile/edit/success', name: 'profile_edit_success', methods: ['POST'])]
    public function edit_profile_success(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserType::class,$this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($this->getUser());
            $em->flush();

            return $this->redirectToRoute("app_profile");
        }
        elseif ($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('warning', 'Vérifier les informations que vous avez renseigné');
            return $this->redirectToRoute('profile_edit_success');
        }

    }


    //Si la modification est annulée
    #[Route('/profile/edit/cancel', name: 'profile_edit_cancel', methods: ['POST', 'GET'])]
    public function edit_profile_cancel(Request $request, EntityManagerInterface $em)
    {
        return $this->redirectToRoute("app_profile");
    }

    /*
    -Page d'édition du mot de passe
    -On envoie dans la base de donnée aprés soumission du formulaire si elle est valide bien sur
    */

    #[Route('/profile/edit_password', name: 'app_change_password', methods: ['POST', 'GET'])]
    public function edit_password(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        //on récupére les informations de l'utilisateur conrant qu'on stocke dans $user
        $user = $this->getUser();
        //On crée une formulaire de type Change passwordType
        $form = $this->createForm(ChangePasswordType::class, $user);

        //On dit au formulaire de gérer les requétes
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //On hash le mot de passe rentré dans le formulaire
            //En vrai hashPassword() prend le mot de passe en plain et le sel cryptologique qui est propre à l'utilisateur
            //Pour générer le nouveau mot de passe que l'on va stocké dans la base de donnée
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )

            );
            //Envoie dans la base de donnée
            $em->flush();
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm(
            'pages/app_change_password.html.twig',
            ['form' => $form]

        );
    }

    //Affiche la page de profil d'un utilisateur en mode view only pas d'édition
    //En attribut si on indique username dans la route et que l'on injecte User
    //symfony sait que l'entité que l'on va utiliser est la méme que celui de la route

    #[Route('/show_profile/{username}', name: 'app_show_profile')]
    public function show_profile(ContactRepository $contactRepo, User $user): Response
    {
        //On regarde d'abord si cet utilisateur est déja contact
        $isContact = false;


        //s'il se trouve dans la liste on enléve le bouton ajouter
        if ($contactRepo->findBy(['user' => $user->getId()])) {

            $isContact = true;
        }

        return $this->render('pages/show_profile.html.twig', ['user' => $user, 'isContact' => $isContact]);
    }
}