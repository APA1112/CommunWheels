<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\DriverRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted('ROLE_DRIVER')]
class AbsenceController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/notificaciones', name: 'notify_main')]
    public function index(AbsenceRepository $absenceRepository, DriverRepository $driverRepository, Request $request): Response
    {
        $userId = $this->getUser()->getDriver();
        $absences = $absenceRepository->findDriverAbsences($userId);

        return $this->render('notify/main.html.twig', [
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

        $driver = $this->security->getUser()->getDriver();

        $absence->setDriver($driver);

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
        return $this->render('notify/modificar.html.twig', [
            'form' => $form->createView(),
            'absence' => $absence,
        ]);
    }
    #[Route('/notificacion/eliminar/{id}', name: 'absence_delete')]
    public function eliminar(Request $request, Absence $absence): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            // Eliminar el grupo (adaptar según tu lógica de eliminación)
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($absence);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}