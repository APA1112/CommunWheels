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

#[IsGranted('ROLE_DRIVER')]
class GroupController extends AbstractController
{
    #[Route('/grupos', name: 'group_main')]
    public function index(GroupRepository $groupRepository): Response{

        $user = $this->getUser();

        $groups = [];

        if ($user->isIsAdmin()) {
            $groups = $groupRepository->groupsData();
        } else {
            $userId = $this->getUser()->getDriver();
            $groups = $groupRepository->findGroupsByDriverId($userId);
        }

        return $this->render('Groups/main.html.twig', ['groups' => $groups]);
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
    #[Route('/grupo/eliminar/{id}', name: 'group_delete')]
    public function eliminar(
        Request $request,
        GroupRepository $groupRepository,
        Group $group): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $groupRepository->remove($group);
                $groupRepository->save();
                $this->addFlash('success', 'Grupo eliminado con éxito');
                return $this->redirectToRoute('group_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar el grupo');
            }
        }
        return $this->render('Groups/eliminar.html.twig', [
            'group' => $group
        ]);
    }
}