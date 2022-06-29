<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Champions;
use App\Entity\ChampLike;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];
        $json_champs = file_get_contents('http://ddragon.leagueoflegends.com/cdn/12.8.1/data/fr_FR/champion.json');
        $champions = json_decode($json_champs);
        $champion = $champions->data;

        for($i=0; $i<10; $i++){
            $user = new User();
            $user->setNickname($faker->name)
                ->setEmail($faker->email)
                ->setPassword('password')
                ->setRegisterDate(new \DateTime())
                ->setRole('ROLE_USER');

            $manager->persist($user);

            $users[] = $user;
        }

        foreach($champion as $v){
            $url_images = 'https://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$v->id.'_0.jpg';

            $champion = new Champions();
            $champion->setName($v->name)
                     ->setType($v->tags[0])
                     ->setTitle($v->title)
                     ->setLore($v->blurb)
                     ->setImage($url_images)
                     ->setDifficulty($v->info->difficulty);
            $manager->persist($champion);
            
            for($j = 1; $j <= mt_rand(2, 5); $j++)
            {
                $comment = new Comment();
                $content = $faker->paragraph();

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt(new \DateTime())
                        ->setChampion($champion);
                $manager->persist($comment);
            }

            for($i=0; $i < mt_rand(0, 20); $i++){
                $like = new ChampLike();
                $like->setChampion($champion)
                    ->setUser($faker->randomElement($users));
    
            $manager->persist($like);
            }
        }
        
        $manager->flush();
    }
}
