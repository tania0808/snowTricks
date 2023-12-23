<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\TrickFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email' => 'nico@gmail.com']);
        UserFactory::createMany(10);
        CategoryFactory::createMany(5);
        TrickFactory::createMany(30);
        CategoryFactory::createMany(10);

        $manager->flush();
    }
}
