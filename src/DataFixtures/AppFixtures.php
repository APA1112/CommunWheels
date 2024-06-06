<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\DriverFactory;
use App\Factory\GroupFactory;
use App\Factory\ScheduleFactory;
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
        // Crear grupos
        GroupFactory::createMany(3);

        // Crear conductores primero
        $drivers = DriverFactory::createMany(10, function () {
            return [
                'groupCollection' => GroupFactory::randomRange(1, 1),
            ];
        });

        // Crear el usuario admin
        $adminUser = UserFactory::createOne([
            'username' => 'admin',
            'password' => $this->passwordHasher->hashPassword(new User(), 'Lvame80d!'),
            'isAdmin' => true,
        ]);

        // Crear usuarios dependientes de los nombres de los conductores
        foreach ($drivers as $driver) {
            $firstName = $driver->getName();
            $lastName = $driver->getLastName();
            $user = new User();
            $username = $user->generateUsername($firstName, $lastName);

            UserFactory::createOne([
                'username' => $username,
                'password' => $this->passwordHasher->hashPassword(new User(), 'cambiame'),
                'isDriver' => true,
                'isAdmin' => false,
                'driver' => $driver,
            ]);
            // Creamos el horario de lunes a viernes para cada conductor
            ScheduleFactory::createMany(5, function() use ($driver) {
                static $day = 1;
                return [
                    'driver' => $driver,
                    'weekDay' => $day++,
                ];
            });
        }

        // Persistir todas las entidades creadas
        $manager->flush();
    }
}
