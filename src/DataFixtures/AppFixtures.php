<?php

namespace App\DataFixtures;

use App\Factory\DriverFactory;
use App\Factory\GroupFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
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

        $manager->flush();
    }
}
