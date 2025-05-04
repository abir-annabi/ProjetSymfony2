<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Faker\Factory;
use App\Entity\Livres;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LivresFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($j=1;$j<=5;$j++){
            $categorie=new Categories();
            $libelle=$faker->word;
            $categorie->setLibelle($libelle)
                      ->setSlug(strtolower(str_replace(' ','-',$libelle)))
                      ->setDescription($faker->text);
        $manager->persist($categorie);//persistance
        for ($i=1;$i<=random_int(15,20);$i++){
        $livre = new Livres();
        $titre= $faker->name();
        $livre->setTitre($titre)
              ->setSlug(strtolower(str_replace(' ','-',$titre)))
              ->setImage("https://picsum.photos/300/?id=".$i)
              ->setResume($faker->text)
              ->setEditeur($faker->company)
              ->setDateEdition($faker->dateTimeBetween('-5 years','now'))
              ->setPrix($faker->randomFloat(2,10,700))
              ->setIsbn($faker->isbn13)
              ->setCat($categorie);
        $manager->persist($livre);//persistance
        }
    }
    $manager->flush();//tabaath lel bd doctrine insert
}
}
