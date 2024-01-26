<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    private array $trickCategories = [
        'Basics',
        'Grabs',
        'Grinds',
        'Butters',
        'Spins',
        'Inverts',
        'Rails',
        'Box Tricks',
        'Flips',
        'Stalls',
        'Jumps',
        'Flat',
        'Rotations',
        'Presses',
    ];


    public function load(ObjectManager $manager): void
    {
        foreach ($this->trickCategories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $this->addReference('category_' . strtolower($categoryName), $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
