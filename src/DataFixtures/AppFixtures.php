<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\DriverFactory;
use App\Factory\GroupFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        GroupFactory::createMany(3);
        DriverFactory::createMany(10, function () {
            return [
                'groupCollection' => GroupFactory::randomRange(1, 1)
            ];
        });

        UserFactory::createOne([
            'username' => 'admin',
            'password' => $this->passwordHasher->hashPassword(new User(), 'admin'),
            'isAdmin' => true,
        ]);
        UserFactory::createMany(9, [
            'password'=>$this->passwordHasher->hashPassword(new User(),'password'),
            'isDriver'=> true,
            'isAdmin' => false,
        ]);
        $manager->flush();
    }
}
