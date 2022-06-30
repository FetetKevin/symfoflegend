<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Champions;
use App\Entity\ChampLike;
use App\Repository\ChampionsRepository;
use App\Repository\ChampLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    public function __construct(ChampionsRepository $repo){
        $this->repo = $repo;
    }

    /**
     * @Route("/liste_des_champions/{type}", name="app_liste_sorted")
     */
    public function listeSorted(String $type): Response
    {
        $champions = $this->repo->findBy(['type' => $type]);
    
        return $this->render('blog/index.html.twig', [
            'champions' => $champions
        ]);
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
    public function formComment(String $name, Comment $comment = null, Request $request, EntityManagerInterface $manager, Security $security)
    {
        $champion = $this->repo->findOneByName($name);
        $user = $security->getUser();

        if(!empty($user)){
            $nickname = $user->getNickname();
        }
        if(!$champion) {
            throw $this->createNotFoundException();
        }
        if(!$comment){
            $comment = new Comment();
        }
        $form = $this->createFormBuilder($comment)
                    ->add('content')
                    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setChampion($champion);
            $comment->setAuthor($nickname);
            $comment->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();
        }
        return $this->render('blog/champion.html.twig', [
            'formComment' => $form->createView(),
            'champion' => $champion
        ]);
    }


    /**
     * Permet de liker ou unliker un champion
     * @Route("/champion/{id}/like", name="app_like")
     */
    public function like(Champions $champion, EntityManagerInterface $manager, ChampLikeRepository $likeRepo): Response
    {
        $user = $this->getUser();

        //if(!$user) return $this->redirectToRoute('app_champion', ['name' => $champion->getName()]);

        if(!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ], 403);
        
        if($champion->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                'champion' => $champion,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            //return $this->redirectToRoute('app_champion', ['name' => $champion->getName()]);
            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $likeRepo->count(['champion' => $champion])
            ], 200);
        }

        $like = new ChampLike();
        $like->setChampion($champion)
             ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        //return $this->redirectToRoute('app_champion', ['name' => $champion->getName()]);
        return $this->json(['code' => 200, 'message' => 'like ajouté', 'likes' => $likeRepo->count(['champion' => $champion])], 200);
    }

}
