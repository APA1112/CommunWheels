<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\DriverRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DRIVER')]
class AbsenceController extends AbstractController
{
    #[Route('/notificaciones', name: 'notify_main')]
    public function index(AbsenceRepository $absenceRepository, DriverRepository $driverRepository, Request $request): Response
    {
        $userId = $this->getUser()->getDriver();
        $absences = $absenceRepository->findDriverAbsences($userId);

        return $this->render('Notify/main.html.twig', [
            'absences' => $absences,
        ]);
    }

    #[Route('/notificacion/nueva', name: 'new_absence')]
    public function new(AbsenceRepository $absenceRepository, Request $request): Response
    {
        $absence = new Absence();
        $absenceRepository->add($absence);
        return $this->modificar($absence, $absenceRepository, $request);
    }

    #[Route('/notificacion/modificar/{id}', name: 'mod_absence')]
    public function modificar(Absence $absence, AbsenceRepository $absenceRepository, Request $request):Response
    {
        $form = $this->createForm(AbsenceType::class, $absence);

        $form->handleRequest($request);

        $nuevo = $absence->getId()===null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $absenceRepository->save();
                if ($nuevo){
                    $this->addFlash('success', 'Notificación de ausencia creada correctamente');
                } else {
                    $this->addFlash('success', 'Ausencia modificada correctamente');
                }
                return $this->redirectToRoute('notify_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('Notify/modificar.html.twig', [
            'form' => $form->createView(),
            'absence' => $absence,
        ]);
    }
    #[Route('/notificacion/eliminar/{id}', name: 'absence_delete')]
    public function eliminar(
        Request $request,
        AbsenceRepository $absenceRepository,
        Absence $absence): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $absenceRepository->remove($absence);
                $absenceRepository->save();
                $this->addFlash('success', 'Notificación de ausencia eliminada con éxito');
                return $this->redirectToRoute('notify_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la notificacion de ausencia');
            }
        }
        return $this->render('Notify/eliminar.html.twig', [
            'absence' => $absence
        ]);
    }
}