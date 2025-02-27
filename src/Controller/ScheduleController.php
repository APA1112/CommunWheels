<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Form\WeekScheduleModel;
use App\Form\WeekScheduleType;
use App\Repository\ScheduleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DRIVER')]
class ScheduleController extends AbstractController
{
    #[Route('/horario', name: 'schedule_main')]
    public function index(ScheduleRepository $scheduleRepository): Response{
        $schedules = $scheduleRepository->findDriverSchedules($this->getUser()->getDriver());
        return $this->render('schedule/main.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/horario/crear', name: 'schedule_create')]
    public function create(ScheduleRepository $scheduleRepository, Request $request): Response
    {
        $driver = $this->getUser()->getDriver();

        for ($i = 1; $i <= 5; $i++) {
            $schedule = new Schedule();
            $schedule->setDriver($driver);
            $schedule->setWeekDay($i);
            $schedule->setEntrySlot(0);
            $schedule->setExitSlot(0);
            $scheduleRepository->add($schedule);
        }

        $scheduleRepository->save(); // Asegúrate de guardar los cambios

        return $this->redirectToRoute('schedule_update', ['id' => $driver->getId()]);
    }

    #[Route('/horario/modificar/{id}', name: 'schedule_update')]
    public function modificar(Driver $driver, ScheduleRepository $scheduleRepository, Request $request): Response
    {
        $week = new WeekScheduleModel();
        $week->schedules = $scheduleRepository->findDriverSchedules($driver);

        $form = $this->createForm(WeekScheduleType::class, $week);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $scheduleRepository->save();
                $this->addFlash('success', 'Cambios guardados con éxito');
                return $this->redirectToRoute('schedule_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }

        return $this->render('schedule/modificar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}