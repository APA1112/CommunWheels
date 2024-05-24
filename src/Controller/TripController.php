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
    #[Route('/cuadrante/viajes/{id}', name: 'app_trip')]
    public function index(TimeTable $timeTable, TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findByTimeTable($timeTable);
        $groupId = $timeTable->getBand()->getId();

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
            'group' => $groupId,
            'timeTable' => $timeTable->getId()
        ]);
    }

    #[Route('/viaje/nuevo/{id}', name: 'trip_new')]
    public function newTrip(
        Group               $group,
        DriverRepository    $driverRepository,
        TripRepository      $tripRepository,
        TimeTableRepository $timeTableRepository
    )
    {
        $timeTable = new TimeTable();
        // Set any required fields here. For example:
        $timeTable->setBand($group);
        // Cogemos la representación numerica del dia de la semana en el que se generan los trips
        $today = new \DateTime();
        $todayDayOfWeek = $today->format('N');
        // Calcula los días hasta el próximo lunes
        $daysToNextMonday = 8 - $todayDayOfWeek;
        if ($daysToNextMonday > 7) {
            $daysToNextMonday -= 7;
        }
        $nextMonday = (clone $today)->add(new \DateInterval('P' . $daysToNextMonday . 'D'));

        $weekStartDate = $nextMonday;

        $timeTable->setWeekStartDate($weekStartDate);
        $timeTable->setActive(1);

        $timeTableRepository->save();

        return $this->render('trip/new.html.twig', [
            'timeTable' => $timeTable,
            'group' => $group,
        ]);
    }
}
