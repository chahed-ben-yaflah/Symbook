<?php

namespace App\DataFixtures;

use App\Entity\Livres;
use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivresFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker=Factory::create('fr_FR');
        for($j=1;$j<=5;$j++){
            $categorie=new Categorie();
            $names=['Roman','Langage C','Base de Données','Histoire','Cuisine'];
            $categorie->setLibelle($names[$j-1])->setSlug(strtolower(str_replace('','-',$names[$j-1])))->setDescription($faker->text);
            $manager->persist($categorie);
        for ($i = 10; $i <=random_int(10,15); $i++) {
            $livre = new Livres();
            $titre=$faker->name;
            $livre->setTitre($titre)
            ->setSlug(strtolower(str_replace('','-',$titre)))
                ->setEditeur($faker->company())
                ->setDateEdition($faker->dateTimeBetween('-5 years','now'))
                ->setResume($faker->text())
                ->setIsbn($faker->isbn13())
                ->setImage('https://picsum.photos/200/?id=' . $i)
                ->setPrix($faker->randomFloat(2,10,700))
                ->setCategorie($categorie);
            $manager->persist($livre); // npersisty a chaque fois baaed nahki mara barka maa l base de donnée => qt de optimisation de la reponse
        }}
        $manager->flush();
     
    }
}
