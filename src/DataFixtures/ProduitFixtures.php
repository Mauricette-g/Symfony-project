<?php

namespace App\DataFixtures;


use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $produitsData = [
            ['nom' => 'Blackbelt**', 'prix' => 29.90, 'image' => 'produit1.jpeg'],
            ['nom' => 'BlueBelt.', 'prix' => 29.90, 'image' => 'produit2.jpeg'],
            ['nom' => 'Street.', 'prix' => 34.50, 'image' => 'produit3.jpeg'],
            ['nom' => 'Pokeball**', 'prix' => 45, 'image' => 'produit4.jpeg'],
            ['nom' => 'PinkLady', 'prix' => 29.90, 'image' => 'produit5.jpeg'],
            ['nom' => 'Snow', 'prix' => 32, 'image' => 'produit6.jpeg'],
            ['nom' => 'Greyback ', 'prix' => 28.50, 'image' => 'produit7.jpeg'],
            ['nom' => 'BlueCloud', 'prix' => 45, 'image' => 'produit8.jpeg'],
            ['nom' => 'BornInUsa **', 'prix' => 59.90, 'image' => 'produit9.jpeg'],
            ['nom' => 'GreenSchool ', 'prix' => 42.20, 'image' => 'produit10.jpeg'],
        ];

        foreach ($produitsData as $index => $data) {
            $produit = new Produit();
            $produit->setNom($data['nom'])
                ->setPrix($data['prix'])
                ->setImage($data['image'])
                ->setFeatured($index < 3)
                ->setTaille(['XS', 'S', 'M', 'L', 'XL']);

            $manager->persist($produit);
        }
        $manager->flush();
    }
}

