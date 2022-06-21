<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/home", name="app_home")
     */
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }
    
    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile(UserRepository $users, CommentRepository $comment): Response
    {
        $usersList = $users->findBy([], ['id' => 'ASC']);
        $commentList = $comment->findBy([], ['id' => 'ASC']);
        return $this->render('home/profile.html.twig', [
            'users' => $usersList,
            'comments' => $commentList
        ]);
    }
}
