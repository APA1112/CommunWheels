<?php

namespace App\Controller;

use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/trip/confirm/{id}', name: 'confirm_trip')]
    public function confirmTrip(Trip $trip, DriverRepository $driverRepository, TripRepository $tripRepository): Response
    {
        $trip = $tripRepository->find($trip->getId());
        if (!$trip) {
            throw $this->createNotFoundException('No se ha encontrado viaje para el id ' . $trip->getId());
        }

        // Verificar que el usuario actual es el conductor del viaje
        if ($trip->getDriver() !== $this->getUser()->getDriver()) {
            throw $this->createAccessDeniedException('No eres el conductor de este viaje.');
        }

        // Incrementar daysDriven
        $driver = $trip->getDriver();
        $driver->setDaysDriven($driver->getDaysDriven() + 1);

        $trip->setActive(false);

        $driverRepository->save();
        //dd($trip->getTimeTable()->getBand());

        return $this->render('trip/new.html.twig', [
            'timeTable' => $trip->getTimeTable(),
            'trips' => $trip->getTimeTable()->getTrips(),
            'group' => $trip->getTimeTable()->getBand(),
        ]);
    }

    #[Route('/trip/{id}', name: 'mod_trip')]
    public function modTrip(Trip $trip, TripRepository $tripRepository): Response
    {
        if (count($trip->getPassengers()) == 0) {
            $drivers = $trip->getPassengers();
            $timeTable = $trip->getTimeTable();
            $tripRepository->remove($trip);
        } else {
            $drivers = $trip->getPassengers();
            $timeTable = $trip->getTimeTable();
        }
        return $this->render('trip/mod.html.twig', [
            'drivers' => $drivers,
            'timeTable' => $timeTable,
            'trip' => $trip,
        ]);
    }
}
