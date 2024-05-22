<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\GroupRepository;
use App\Repository\TimeTableRepository;
use App\Repository\TripRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DRIVER')]
class TimeTableController extends AbstractController
{
    #[Route('/cuadrantes/{id}', name: 'timetable_main')]
    public function modificar(Group $group, TimeTableRepository $timeTableRepository):Response
    {
        $timeTablesGroup = $timeTableRepository->findByGroup($group);

        return $this->render('timeTable/modificar.html.twig', [
            'timeTablesGroup' => $timeTablesGroup,
            'group' => $group
        ]);
    }
}