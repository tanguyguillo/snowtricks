<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * class fixture (testing)
 * php bin/console doctrine:fixtures:load.... to see make:fixtures
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $catList = ['Stalls', 'Straight Airs', 'Grabs', 'Spins', "Flips and inversed rotations", 'Slides', "Tweats and variations", "Inverted hand plants", "Else"];
        $slugger = new AsciiSlugger();

        foreach ($catList as $valeur) {
            $category = new Category();
            $category->setName($valeur);
            $slug = $slugger->slug($valeur);
            $category->setslug($slug);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
