<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++)
        {
            $annonce = new Annonce();
            $annonce->setTitle("Titre de l'annonce n°$i")
                    ->setContent("Contenu de l'aticle n°$i")
                    ->setImage("http://placehold.it/350x150")
                    ->setCreatedAt( new \DateTime() );
            
            $manager->persist($annonce);
        }

        $manager->flush();
    }
}
