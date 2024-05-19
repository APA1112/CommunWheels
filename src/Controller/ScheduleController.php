<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function create(ScheduleRepository $scheduleRepository, Request $request):Response{
        $schedule = new Schedule();
        $scheduleRepository->add($schedule);
        $schedule->setDriver($this->getUser()->getDriver());
        return $this->modificar($schedule, $scheduleRepository, $request);
    }
    #[Route('/horario/modificar/{id}', name: 'schedule_update')]
    public function modificar(ScheduleRepository $scheduleRepository, Request $request): Response
    {
        $schedules = $scheduleRepository->findDriverSchedules($this->getUser()->getDriver());

        if (!$schedules) {
            $this->addFlash('error', 'No se han encontrado horarios para el conductor especificado.');
            return $this->redirectToRoute('schedule_main');
        }

        $form = $this->createForm(ScheduleType::class, $schedules[0]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($schedules as $schedule) {
                    $scheduleRepository->save($schedule);
                }
                $this->addFlash('success', 'Cambios guardados con éxito');
                return $this->redirectToRoute('schedule_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        //dd($schedules);
        return $this->render('schedule/modificar.html.twig', [
            'form' => $form->createView(),
            'schedules' => $schedules
        ]);
    }
}