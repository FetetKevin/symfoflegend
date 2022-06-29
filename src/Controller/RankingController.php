<?php

namespace App\Controller;

use App\Entity\Champions;
use App\Repository\ChampionsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RankingController extends AbstractController
{
    /**
     * @Route("/ranking", name="app_ranking")
     */
    public function index(ChampionsRepository $repo): Response
    {
        //$champions = $repo->findBy([],[],$limit = 5);
        $champions = $repo->findAll();

        
        return $this->render('ranking/index.html.twig', [
            'champions' => $champions
        ]);
    }
}
