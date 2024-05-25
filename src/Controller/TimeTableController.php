<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\GroupRepository;
use App\Repository\TimeTableRepository;
use App\Repository\TripRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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