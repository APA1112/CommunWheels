<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\AbsenceRepository;
use App\Repository\DriverRepository;
use App\Repository\GroupRepository;
use App\Repository\NonSchoolDayRepository;
use App\Repository\ScheduleRepository;
use App\Repository\TimeTableRepository;
use App\Repository\TripRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DRIVER')]
class TimeTableController extends AbstractController
{
    #[Route('/cuadrantes/{id}', name: 'timetable_main')]
    public function modificar(Group $group, TimeTableRepository $timeTableRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $timeTablesGroup = $timeTableRepository->findByGroup($group);
        $query = $timeTableRepository->getTimeTablePagination($group);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('timeTable/modificar.html.twig', [
            'timeTablesGroup' => $timeTablesGroup,
            'pagination' => $pagination,
            'group' => $group
        ]);
    }

    #[Route('cuadrante/{id}', name: 'see_timetable')]
    public function moreInfo(TimeTable $timeTable): Response
    {
        $group = $timeTable->getBand();
        return $this->render('trip/new.html.twig', [
            'timeTable' => $timeTable,
            'group' => $group,
        ]);
    }

    #[Route('/cuadrante/nuevo/{id}', name: 'timeTable_new')]
    public function newT(
        Group                  $group,
        AbsenceRepository      $absenceRepository,
        ScheduleRepository     $scheduleRepository,
        NonSchoolDayRepository $nonSchoolDayRepository,
        DriverRepository       $driverRepository,
        TripRepository         $tripRepository,
        TimeTableRepository    $timeTableRepository,
        ValidatorInterface     $validator
    )
    {
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
        // Vemos si ya existe un timeTable con la misma fecha
        $existingTimeTable = $timeTableRepository->findByWeekStartDate($weekStartDate);
        if ($existingTimeTable) {
            $this->addFlash('error', 'Ya existe un cuadrante para esta semana.');
            return $this->redirectToRoute('timetable_main', ['id' => $group->getId()]);
        }

        $timeTable = new TimeTable();
        // Set any required fields here. For example:
        $timeTable->setBand($group);

        $timeTable->setWeekStartDate($weekStartDate);
        $timeTable->setActive(1);

        $errors = $validator->validate($timeTable);

        if (count($errors) > 0) {
            $this->addFlash('error', 'Ha habido un error creando el cuadrante.');
            return $this->redirectToRoute('timetable_main', ['id' => $group->getId()]);
        }
        $timeTableRepository->add($timeTable);

        $drivers = $driverRepository->findDriversByGroupOrderedByDaysDriven($group);

        $tripDriverSchedules = $scheduleRepository->findDriverSchedules($drivers[0]);
        $tripDriverAbsences = $absenceRepository->findDriverAbsences($drivers[0]);
        $groupNonSchoolDays = $nonSchoolDayRepository->findByGroup($group);
        $groupNonSchoolDaysDates = [];
        $driverAbsencesDates = [];

        foreach ($groupNonSchoolDays as $groupNonSchoolDay) {
            $groupNonSchoolDaysDates[] = $groupNonSchoolDay->getDayDate();
        }

        foreach ($tripDriverAbsences as $driverAbsence) {
            $driverAbsencesDates[] = $driverAbsence->getAbsenceDate();
        }

        // Preformatear las fechas fuera del bucle
        $formattedDriverAbsencesDates = array_map(function ($date) {
            return $date->format('Y-m-d');
        }, $driverAbsencesDates);

        $formattedGroupNonSchoolDaysDates = array_map(function ($date) {
            return $date->format('Y-m-d');
        }, $groupNonSchoolDaysDates);

        for ($i = 0; $i < 5; $i++) {
            $trip = new Trip();
            $trip->setTripDate($weekStartDate);
            $trip->setTimeTable($timeTable);

            // Convertir weekStartDate a una cadena de formato 'Y-m-d' para comparar
            $formattedWeekStartDate = $weekStartDate->format('Y-m-d');

            // Verificar si la fecha no es un día no escolar ni una ausencia del conductor
            $isNonSchoolDay = in_array($formattedWeekStartDate, $formattedGroupNonSchoolDaysDates);
            $isDriverAbsent = in_array($formattedWeekStartDate, $formattedDriverAbsencesDates);
            $driverAvailable = $tripDriverSchedules[$i]->getEntrySlot() != 0;

            if (!$isNonSchoolDay) {
                if (!$isDriverAbsent && $driverAvailable) {
                    $driver = $drivers[0];
                    $entrySlot = $tripDriverSchedules[$i]->getEntrySlot();
                    $exitSlot = $tripDriverSchedules[$i]->getExitSlot();
                } else {
                    // Encontrar el primer conductor disponible
                    foreach ($drivers as $driver) {
                        if ($driver !== $drivers[0]) {
                            $entrySlot = $driver->getSchedules()[$i]->getEntrySlot();
                            $exitSlot = $driver->getSchedules()[$i]->getExitSlot();
                            if ($entrySlot != 0) {
                                break;
                            }
                        }
                    }
                }
            } else {
                $driver = $drivers[0];
                $entrySlot = 0;
                $exitSlot = 0;
            }

            $trip->setDriver($driver);
            $trip->setEntrySlot($entrySlot);
            $trip->setExitSlot($exitSlot);

            $tripRepository->add($trip);
            $weekStartDate->modify('+1 day');
        }



        return $this->render('trip/new.html.twig', [
            'timeTable' => $timeTable,
            'group' => $group,
            'success' => true,
        ]);
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/cuadrante/eliminar/{id}', name: 'timetable_delete')]
    public function eliminar(Request $request, TimeTable $timeTable, TimeTableRepository $timeTableRepository): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            $timeTableRepository->remove($timeTable);

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}