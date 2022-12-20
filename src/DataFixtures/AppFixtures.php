<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



/**
 * class fixture (testing)
 * php bin/console doctrine:fixtures:load
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category ->setName('test');
        $category ->setslug('test-slug');

        $manager->persist($category);

        $category = new Category();
        $category ->setName('test2');
        $category ->setslug('test-slug2');

        $manager->persist($category);

        $manager->flush();
    }
}
