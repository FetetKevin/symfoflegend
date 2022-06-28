<?php

namespace App\Controller;

use App\Repository\ChampionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIController extends AbstractController
{
    /**
     * @Route("/api", name="app_api")
     */
    public function index(ChampionsRepository $repo): JsonResponse
    {
        $champions = $repo->findAll();
        foreach($champions as $champion){
            $arrayCollection[] = array(
                'id' => $champion->getId(),
                'name' => $champion->getName(),
                'title' => $champion->getTitle(),
                'lore' => $champion->getLore(),
                'image' => $champion->getImage(),
                'Type' => $champion->getType(),
                'Difficulty' => $champion->getDifficulty(),
            );
        }

        return new JsonResponse($arrayCollection);
    }
}
