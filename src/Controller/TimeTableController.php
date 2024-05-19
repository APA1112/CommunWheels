<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\GroupRepository;
use App\Repository\TimeTableRepository;
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
    public function modificar(Group $group, TimeTableRepository $timeTableRepository, Request $request):Response
    {
        $timeTablesGroup = $timeTableRepository->findByGroup($group->getId());

        //dd($timeTablesGroup);

        return $this->render('timeTable/modificar.html.twig', [
            'timeTablesGroup' => $timeTablesGroup,
        ]);
    }
}