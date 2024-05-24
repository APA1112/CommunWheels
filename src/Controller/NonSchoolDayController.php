<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\NonSchoolDay;
use App\Form\NonSchoolDayType;
use App\Repository\NonSchoolDayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NonSchoolDayController extends AbstractController
{

    #[Route('/nonschoolday/nuevo', name: 'nonschoolday_new')]
    public function nuevo(NonSchoolDayRepository $nonSchoolDayRepository, Request $request): Response
    {
        $nonSchoolDay = new NonSchoolDay();
        $nonSchoolDayRepository->add($nonSchoolDay);
        return $this->modificar($nonSchoolDay, $request, $nonSchoolDayRepository);
    }

    #[Route('/nonschoolday/{id}', name: 'app_non_school_day')]
    public function index(Group $group, NonSchoolDayRepository $nonSchoolDayRepository): Response
    {
        $nonSchoolDays = $nonSchoolDayRepository->findByGroup($group);

        return $this->render('non_school_day/index.html.twig', [
            'group'=>$group,
            'nonSchoolDays'=>$nonSchoolDays,
        ]);
    }
    #[Route('/nonschoolday/modificar/{id}', name: 'nonschoolday_edit')]
    public function modificar(NonSchoolDay $nonSchoolDay, Request $request, NonSchoolDayRepository $nonSchoolDayRepository) : Response {

        $form = $this->createForm(NonSchoolDayType::class, $nonSchoolDay);

        $form->handleRequest($request);

        $nuevo = $nonSchoolDay->getId()===null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $nonSchoolDayRepository->save();
                if ($nuevo) {
                    $this->addFlash('success', 'Dia festivo creado con éxito');
                } else {
                    $this->addFlash('success', 'Cambios guardados con éxito');
                }
                return $this->redirectToRoute('app_non_school_day');
            } catch (\Exception $e){
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('non_school_day/modificar.html.twig', [
            'form' => $form->createView(),
            'nonSchoolDay' => $nonSchoolDay,
        ]);
    }

    #[Route('/nonschoolday/eliminar/{id}', name: 'nonschoolday_delete', methods: 'delete')]
    public function delete(Request $request, NonSchoolDay $nonSchoolDay, NonSchoolDayRepository $nonSchoolDayRepository): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            $nonSchoolDayRepository->remove($nonSchoolDay);
            return new JsonResponse(['status' => 'Group deleted'], 200);
        }
        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}
