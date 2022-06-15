<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Champions;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $json_champs = file_get_contents('http://ddragon.leagueoflegends.com/cdn/12.8.1/data/fr_FR/champion.json');
        $champions = json_decode($json_champs);
        $champion = $champions->data;

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
            
        }

        $manager->flush();
    }
}
