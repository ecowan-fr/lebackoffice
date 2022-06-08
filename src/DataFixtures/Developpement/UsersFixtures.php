<?php

namespace App\DataFixtures\Developpement;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture implements FixtureGroupInterface {

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public static function getGroups(): array {
        return ['developpement'];
    }

    public function load(ObjectManager $manager): void {
        /** @var Generator $faker */
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 500; $i++) {
            $user = (new User)
                ->setCreatedMethod($faker->randomElement(['console', 'azure', 'user']))
                ->setEmail($faker->email())
                ->setLastname($faker->lastName())
                ->setFirstname($faker->firstName())
                ->setPassword('password');

            $manager->persist($user);
        }

        $manager->flush();
    }
}
