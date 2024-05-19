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
    #[Route('/cuadrante', name: 'timetable_main')]
    public function index(GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();

        if ($user->isIsGroupAdmin() || $user->isIsAdmin()) {
            $groups = $groupRepository->groupsData();
        } else {
            $userId = $this->getUser()->getDriver();
            $groups = $groupRepository->findGroupsByDriverId($userId);
        }

        return $this->render('timeTable/main.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/cuadrantes/filtrados', name: 'timeTable_filter')]
    public function filterIndex(GroupRepository $groupRepository, PaginatorInterface $paginator, Request $request):Response{

        $groups = $groupRepository->findGroupsByDriverId($this->getUser()->getDriver());
        $query = $groupRepository->getDriversGroupPagination($this->getUser()->getDriver());
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('timeTable/main.html.twig',
            [
                'groups' => $groups,
                'pagination' => $pagination,
            ]);
    }
    #[Route('/cuadrante/mod/{id}', name: 'timetable_mod')]
    public function modificar(Group $group, TimeTableRepository $timeTableRepository, Request $request):Response
    {
        $timeTablesGroup = $timeTableRepository->findByGroup($group->getId());

        return $this->render('timeTable/modificar.html.twig', [
            'timeTablesGroup' => $timeTablesGroup,
            'group' => $group->getId()
        ]);
    }
}