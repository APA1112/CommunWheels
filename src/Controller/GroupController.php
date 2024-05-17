<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[IsGranted('ROLE_DRIVER')]
class GroupController extends AbstractController
{
    #[Route('/grupos', name: 'group_main')]
    public function index(GroupRepository $groupRepository): Response{

        $user = $this->getUser();

        $groups = [];

        if ($user->isIsGroupAdmin() || $user->isIsAdmin()) {
            $groups = $groupRepository->groupsData();
        } else {
            $userId = $this->getUser()->getDriver();
            $groups = $groupRepository->findGroupsByDriverId($userId);
        }

        return $this->render('Groups/main.html.twig', ['groups' => $groups]);
    }

    #[Route('/grupos/filtrados', name: 'group_filter')]
    public function filterIndex(GroupRepository $groupRepository):Response{
        $userId = $this->getUser()->getDriver();
        $groups = $groupRepository->findGroupsByDriverId($userId);

        return $this->render('Groups/main.html.twig',
            [
                'groups' => $groups,
                'userId' => $userId
            ]);
    }
    #[Route('/grupo/nuevo', name: 'group_new')]
    public function nuevo(GroupRepository $groupRepository, Request $request): Response
    {
        $group = new Group();
        $groupRepository->add($group);
        return $this->modificar($group, $groupRepository, $request);
    }

    #[Route('/grupo/modificar/{id}', name: 'group_mod')]
    public function modificar(Group $group, GroupRepository $groupRepository, Request $request):Response
    {
        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        $nuevo = $group->getId()===null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $group->setName($group->getOrigin() . '-' . $group->getDestination());
                $groupRepository->save();
                if ($nuevo) {
                    $this->addFlash('success', 'Grupo creado con exito');
                } else {
                    $this->addFlash('success', 'Cambios guardados con exito');
                }
                return $this->redirectToRoute('group_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('Groups/modificar.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
        ]);
    }
    #[Route('/grupo/eliminar/{id}', name: 'group_delete', methods: 'delete')]
    public function delete(Request $request, Group $group): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            // Eliminar el grupo (adaptar según tu lógica de eliminación)
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}