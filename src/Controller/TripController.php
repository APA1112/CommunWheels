<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\TimeTable;
use App\Repository\DriverRepository;
use App\Repository\TimeTableRepository;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/cuadrante/{id}', name: 'app_trip')]
    public function index(TimeTable $timeTable, TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findByTimeTable($timeTable->getId());
        $groupId = $timeTable->getBand()->getId();

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
            'group' => $groupId,
            'timeTable' => $timeTable->getId()
        ]);
    }

    #[Route('/cuadrante/nuevo/{id}', name: 'trip_new')]
    public function new(TimeTable $timeTable, DriverRepository $driverRepository, TimeTableRepository $timeTableRepository, Group $group): Response
    {
        //$timeTable = $timeTableRepository->findByGroup($group->getId());

        if ($group->getId()) {
            throw $this->createNotFoundException('No se han encontrado cuadrantes');
        } else {
            // Create a new TimeTable
            $timeTable = new TimeTable();
            // Set any required fields here. For example:
            $timeTable->setBand($group->getId());
            $timeTable->setWeekStartDate(new \DateTime('now'));
            // Save the new TimeTable
            $timeTableRepository->save();
        }

        $drivers = $timeTable->getBand()->getDrivers();

        // Inicializar arrays para absences y waitTimes
        $absences = [];
        $waitTimes = [];
        $schedules = [];
        $daysDriven = [];

        // Obtener absences y waitTimes para cada conductor
        foreach ($drivers as $driver) {
            $absences[$driver->getId()] = $driverRepository->getDriverAbsences($driver->getId())->getAbsences();
            $waitTimes[$driver->getId()] = $driver->getWaitTime();
            $daysDriven[$driver->getId()] = $driver->getDaysDriven();
            $schedules[$driver->getId()] = $driverRepository->getDriverSchedule($driver->getId())->getSchedules();
        }

        return $this->render('trip/new.html.twig', [
            'drivers' => $drivers,
            'schedules' => $schedules,
            'absences' => $absences,
            'waitTimes' => $waitTimes,
            'daysDriven' => $daysDriven,
            'timeTableId' => $timeTable->getId()
        ]);
    }
}
