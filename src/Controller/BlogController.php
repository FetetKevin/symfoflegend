<?php

namespace App\Controller;

use App\Entity\Champions;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChampionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BlogController extends AbstractController
{
    public function __construct(ChampionsRepository $repo){
        $this->repo = $repo;
    }
    /**
     * @Route("/liste_des_champions", name="app_liste")
     */
    public function liste(): Response
    {
        $champions = $this->repo->findBy([], ['name' => 'ASC']);
    
        return $this->render('blog/index.html.twig', [
            'champions' => $champions
        ]);
    }

    /**
     * @Route("/champion/ajouter", name="app_ajouter")
     * @Route("/champion/{name}/modifier", name="app_modifier")
     */
    public function formChamp(Champions $champion = null, Request $request, EntityManagerInterface $manager)
    {

        if(!$champion){
            $champion = new Champions();
        }
    
        $form = $this->createFormBuilder($champion)
                    ->add('name')
                    ->add('title')
                    ->add('type', ChoiceType::class, [
                        'choices'  => [
                            'Mage' => 'Mage',
                            'Fighter' => 'Fighter',
                            'Assassin' => 'Assassin',
                            'Tank' => 'Tank',
                            'Support' => 'Support',
                        ],
                    ])
                    ->add('difficulty', IntegerType::class)
                    ->add('lore')
                    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $champion->setImage('https://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$request->request->all('form')['name'].'_0.jpg');

            $manager->persist($champion);
            $manager->flush();

            return $this->redirectToRoute('app_champion', ['name' => $champion->getName()]);
        }

        return $this->render('blog/formChamp.html.twig', [
            'formChamp' => $form->createView(),
            'modify' => $champion->getId() !== null
        ]);
    }

    /**
     * @Route("/champion/{name}", name="app_champion")
     */
    public function unique(String $name): Response
    {
        $champion = $this->repo->findOneByName($name);

        if(!$champion) {
            throw $this->createNotFoundException();
        }

        return $this->render('blog/champion.html.twig', [
            'champion' => $champion
        ]);
    }

}
