<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/grupos', name: 'group_main')]
    public function index(GroupRepository $groupRepository): Response{
        $groups = $groupRepository->groupsData();
        return $this->render('Groups/main.html.twig', ['groups' => $groups]);
    }

    #[Route('/grupos/{id}', name: 'group_mod')]
    public function modificar(int $id, GroupRepository $groupRepository):Response
    {
        $group = $groupRepository->findGroupById($id);
        return $this->render('Groups/modificar.html.twig', ['group' => $group]);
    }
}