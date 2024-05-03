<?php

namespace App\Controller;

use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    #[Route('/horario', name: 'schedule_main')]
    public function index(ScheduleRepository $scheduleRepository): Response{
        $userId = $this->getUser()->getDriver()->getId();
        $schedules = $scheduleRepository->findDriverSchedules($userId);
        return $this->render('Schedule/main.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/horario/crear', name: 'schedule_create')]
    public function create():Response{
        return $this->render('Schedule/modificar.html.twig');
    }
}