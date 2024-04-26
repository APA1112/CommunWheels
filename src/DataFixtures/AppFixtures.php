<?php

namespace App\DataFixtures;

use App\Factory\DriverFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        DriverFactory::createMany(10);

        $manager->flush();
    }
}
