<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Factory\CategoryFactory;
use App\Factory\TrickFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHacher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setFirstName('Jane');
            $user->setLastName('Doe');
            $user->setUsername('jane_doe123' . $i);
            $user->setEmail('jane_doe' . $i . '@gmail.com');
            $user->setIsVerified(true);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'admin123')
            );

            $this->addReference('user_' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
