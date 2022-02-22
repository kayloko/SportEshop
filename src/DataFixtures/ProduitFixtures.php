<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 6; $i++) {
            $produit = new Produit();
            $produit->setNom($i)
                ->setDescr("testestestestestestestes...")
                ->setPrix("999")
                ->setImage("https://dummyimage.com/450x300/dee2e6/6c757d.jpg")
                ->setCreatedAt(new \DateTime);
            $manager->persist($produit);
        }

        $manager->flush();
    }
}