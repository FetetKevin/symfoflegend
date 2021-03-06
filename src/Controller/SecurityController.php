<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder)
    {  

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $clearPass = $user->getPassword();
            $hash = $encoder->hashPassword($user, $clearPass);

            $user->setPassword($hash);
            $user->setRegisterDate(new \DateTime());
            $user->setRole('ROLE_USER');

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration.html.twig', [
            'formRegistration' => $form->createView(),
            'modify' => false
        ]);
    }

    /**
     * @Route("/profile/modifier", name="app_profile_modifier")
     */
    public function update(Security $security, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder)
    {  
        $user = $security->getUser();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $clearPass = $user->getPassword();
            $hash = $encoder->hashPassword($user, $clearPass);
            $user->setPassword($hash);
            $user->setRole('ROLE_USER');
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_profile', ['name' => $this->getUser()->getNickname()]);
        }

        return $this->render('security/registration.html.twig', [
            'formRegistration' => $form->createView(),
            'modify' => $security->getUser() !== null
        ]);
    }

    /**
     * @Route("/profile/{name}", name="app_profile")
     */
    public function profile(String $name, UserRepository $users, CommentRepository $comment)
    {
        $usersList = $users->findBy([], ['id' => 'ASC']);
        $commentList = $comment->findBy([], ['id' => 'ASC']);
        $userComms = $comment->findBy(['author' => $name]);

        return $this->render('home/profile.html.twig', [
            'users' => $usersList,
            'comments' => $commentList,
            'userComms' => $userComms
        ]);
    }

    /**
     * @Route("/commentaire/{id}/delete", name="app_delete_comm")
     */
    public function delete_commentaire(Comment $comm, EntityManagerInterface $manager)
    {
        $manager->remove($comm);
        $manager->flush();

        return $this->redirectToRoute('app_profile', ['name' => $this->getUser()->getNickname()]);
    }

    /**
     * @Route("/user/{id}/delete", name="app_delete_user")
     */
    public function delete_user(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('app_profile', ['name' => $this->getUser()->getNickname()]);
    }

    /**
     * @Route("/connexion", name="app_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(){}
}
