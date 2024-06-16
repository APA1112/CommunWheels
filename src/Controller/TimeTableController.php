<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\AbsenceRepository;
use App\Repository\DriverRepository;
use App\Repository\NonSchoolDayRepository;
use App\Repository\ScheduleRepository;
use App\Repository\TimeTableRepository;
use App\Repository\TripRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DRIVER')]
class TimeTableController extends AbstractController
{
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

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
            'trips' => $timeTable->getTrips(),
            'group' => $group,
        ]);
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/cuadrante/nuevo/{id}', name: 'timeTable_new')]
    public function newTrip(
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
        $trips = [];
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
        $existingTimeTable = $timeTableRepository->findByWeekStartDate($weekStartDate, $group);
        if ($existingTimeTable) {
            $this->addFlash('error', 'Ya existe un cuadrante para esta semana.');
            return $this->redirectToRoute('timetable_main', ['id' => $group->getId()]);
        }

        $drivers = $driverRepository->findDriversByGroupOrderedByDaysDriven($group);

        if (empty($drivers)) {
            $this->addFlash('error', 'No puedes generar un cuadrante para un grupo sin conductores.');
            return $this->redirectToRoute('timetable_main', ['id' => $group->getId()]);
        }

        $timeTable = new TimeTable();
        $timeTable->setBand($group);
        $timeTable->setWeekStartDate($weekStartDate);
        $timeTable->setActive(1);

        $errors = $validator->validate($timeTable);

        if (count($errors) > 0) {
            $this->addFlash('error', 'Ha habido un error creando el cuadrante.');
            return $this->redirectToRoute('timetable_main', ['id' => $group->getId()]);
        }
        $timeTableRepository->add($timeTable);

        $groupNonSchoolDays = $nonSchoolDayRepository->findByGroup($group);
        $groupNonSchoolDaysDates = [];
        $driverAbsencesDates = [];

        foreach ($groupNonSchoolDays as $groupNonSchoolDay) {
            $groupNonSchoolDaysDates[] = $groupNonSchoolDay->getDayDate();
        }

        $formattedGroupNonSchoolDaysDates = array_map(function ($date) {
            return $date->format('Y-m-d');
        }, $groupNonSchoolDaysDates);

        $schedulesGroups = [];

        //Agrupamos los conductores por el horario de cada dia
        for ($i = 0; $i < 5; $i++) {
            $scheduleGroups = [];
            foreach ($drivers as $driver) {
                $schedule = $driver->getSchedules()[$i];
                $entrySlot = $schedule->getEntrySlot();
                $exitSlot = $schedule->getExitSlot();
                $waitTime = $driver->getWaitTime();

                if ($entrySlot == 0 || $exitSlot == 0) {
                    continue;
                }

                // Ajustar entrySlot y exitSlot
                $entrySlotAdjusted = ($entrySlot == 1) ? $entrySlot : $entrySlot - $waitTime;
                $exitSlotAdjusted = ($exitSlot == 1) ? $exitSlot : $exitSlot + $waitTime;

                // Crear clave basada en los slots originales y ajustados
                $originalKey = $entrySlot . '-' . $exitSlot;
                $adjustedKey1 = $entrySlotAdjusted . '-' . $exitSlot;
                $adjustedKey2 = $entrySlot . '-' . $exitSlotAdjusted;
                $adjustedKey3 = $entrySlotAdjusted . '-' . $exitSlotAdjusted;

                // Verificar si alguna clave existente coincide con las posibles combinaciones
                $foundKey = false;
                foreach ([$originalKey, $adjustedKey1, $adjustedKey2, $adjustedKey3] as $key) {
                    if (isset($scheduleGroups[$key])) {
                        $scheduleGroups[$key][] = $driver;
                        $foundKey = true;
                        break;
                    }
                }

                // Si no se encontró ninguna clave existente, crear una nueva clave con los valores originales
                if (!$foundKey) {
                    if (!isset($scheduleGroups[$originalKey])) {
                        $scheduleGroups[$originalKey] = [];
                    }
                    $scheduleGroups[$originalKey][] = $driver;
                }
            }
            $schedulesGroups[$i][] = $scheduleGroups;
        }

        //Generamos un trip para cada dia de la semana
        //Recorremos los horarios de cada dia de la semana
        foreach ($schedulesGroups as $key => $schedulesGroup) {
            // Clonamos la fecha de inicio de la semana y la ajustamos para el día actual del bucle
            $currentTripDate = (clone $weekStartDate)->modify('+' . $key . ' days');

            //Recorremos cada agrupacion de horarios
            foreach ($schedulesGroup as $scheduleGroup) {
                //Recorremos los conductores en la agrupacion
                foreach ($scheduleGroup as $driverGroup) {
                    $tripDriverSchedules = $scheduleRepository->findDriverSchedules($driverGroup[0]);
                    $tripDriverAbsences = $absenceRepository->findDriverAbsences($driverGroup[0]);

                    foreach ($tripDriverAbsences as $driverAbsence) {
                        $driverAbsencesDates[] = $driverAbsence->getAbsenceDate();
                    }

                    // Preformatear las fechas
                    $formattedDriverAbsencesDates = array_map(function ($date) {
                        return $date->format('Y-m-d');
                    }, $driverAbsencesDates);

                    $trip = new Trip();
                    $trip->setTripDate($currentTripDate);
                    $trip->setTimeTable($timeTable);
                    $trip->setActive(true);

                    // Convertir currentTripDate a una cadena de formato 'Y-m-d' para comparar
                    $formattedCurrentTripDate = $currentTripDate->format('Y-m-d');

                    // Verificar si la fecha no es un día no escolar ni una ausencia del conductor
                    $isNonSchoolDay = in_array($formattedCurrentTripDate, $formattedGroupNonSchoolDaysDates);
                    $isDriverAbsent = in_array($formattedCurrentTripDate, $formattedDriverAbsencesDates);
                    $driverAvailable = $tripDriverSchedules[$key]->getEntrySlot() != 0;

                    if (!$isNonSchoolDay) {
                        if (!$isDriverAbsent && $driverAvailable) {
                            $driver = $driverGroup[0];
                            $entrySlot = $tripDriverSchedules[$key]->getEntrySlot();
                            $exitSlot = $tripDriverSchedules[$key]->getExitSlot();
                        } else {
                            // Encontrar el primer conductor disponible
                            foreach ($driverGroup as $driver) {
                                if ($driver !== $driverGroup[0]) {
                                    $driverSchedule = $driver->getSchedules()[$key];
                                    $entrySlot = $driverSchedule->getEntrySlot();
                                    $exitSlot = $driverSchedule->getExitSlot();
                                    if ($entrySlot != 0) {
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $driver = $driverGroup[0];
                        $entrySlot = 0;
                        $exitSlot = 0;
                    }

                    $trip->setDriver($driver);
                    $trip->setEntrySlot($entrySlot);
                    $trip->setExitSlot($exitSlot);

                    foreach ($driverGroup as $passDriver) {
                        if ($passDriver !== $driver && count($trip->getPassengers()) < $driver->getSeats()) {
                            $trip->addPassenger($passDriver);
                        }
                    }

                    $tripRepository->add($trip);
                    $trips[] = $trip;
                }
            }
        }
        /*
        foreach ($drivers as $driver) {
            // Enviar correo al nuevo conductor
            $email = (new TemplatedEmail())
                ->from(new Address('commun.wheels@gmail.com', 'CommunWheels'))
                ->to($driver->getEmail())
                ->subject('Nuevo cuadrante generado para ' . $driver->getName())
                ->htmlTemplate('emails/new_schedule.html.twig')
                ->context([
                    'driver_name' => $driver->getName(),
                    'start_date' => $timeTable->getWeekStartDate()->format('d-m-Y'),
                    'group' => $group->getName(),
                    'support_email' => 'commun.wheels@gmail.com',
                    'year' => date('Y')
                ]);

            $this->mailer->send($email);

        }
        */
        return $this->render('trip/new.html.twig', [
            'timeTable' => $timeTable,
            'trips' => $trips,
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
