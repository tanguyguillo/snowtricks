<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * class fixture 
 * php bin/console doctrine:fixtures:load
 */
class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $catList = ['Stalls', 'Straight Airs', 'Grabs', 'Spins', "Flips and inversed rotations", 'Slides', "Tweats and variations", "Inverted hand plants", "Else"];
        $slugger = new AsciiSlugger();

        foreach ($catList as $value) {
            $category = new Category();
            $category->setName($value);
            $value = strtolower($value);
            $slug = $slugger->slug($value);
            $category->setSlug($slug);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
