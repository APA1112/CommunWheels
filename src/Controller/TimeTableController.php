<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\GroupRepository;
use App\Repository\TripRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DRIVER')]
class TimeTableController extends AbstractController
{
    #[Route('/cuadrante', name: 'timetable_main')]
    public function index(GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->groupsData();

        return $this->render('timeTable/main.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route('/cuadrante/mod/{id}', name: 'timetable_mod')]
    public function modificar(Group $group, GroupRepository $groupRepository, Request $request):Response
    {
        $timeTablesGroup = $groupRepository->findGroupTimeTables($group->getId());

        //dd($timeTablesGroup);

        return $this->render('timeTable/modificar.html.twig', [
            'timeTablesGroup' => $timeTablesGroup,
        ]);
    }
    public function new(
        int $groupId,
        DriverRepository $driverRepository,
        GroupRepository $groupRepository,
        TripRepository $tripRepository,
        Request $request
    ): Response {
        // Getting the group by ID
        $group = $groupRepository->find($groupId);
        if (!$group) {
            return new Response("Group not found", 404);
        }

        // Getting group drivers
        $groupDrivers = $groupRepository->findGroupDrivers($group->getId());

        // Filter drivers who have absences
        $availableDrivers = array_filter($groupDrivers, function ($driver) {
            // Checking for driver absences
            foreach ($driver->getAbsences() as $absence) {
                if ($absence->getAbsenceDate() === (new \DateTime())->format('Y-m-d')) {
                    return false;
                }
            }
            return true;
        });

        // Selecting driver
        $selectDriver = null;
        foreach ($availableDrivers as $driver) {
            if ($selectDriver === null || $driver->getDaysDriven() < $selectDriver->getDaysDriven()) {
                $selectDriver = $driver;
            }
        }

        // Generating Trip
        if ($selectDriver !== null) {
            $trip = new Trip();
            $trip->setTripDate(new \DateTime());
            $trip->setEntrySlot($selectDriver->getEntrySlot());
            $trip->setExitSlot($selectDriver->getExitSlot());
            $trip->setTimeTable($group->getTimeTables()); // Assuming you need to set the TimeTable

            // Persisting the trip in the database
            $tripRepository->save();

            return new Response("Trip created successfully", 201);
        }

        return new Response("No available driver found", 400);
    }
}