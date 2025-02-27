<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(GroupRepository $groupRepository, PaginatorInterface $paginator, Request $request): Response{

        $user = $this->getUser();

        $groups = [];

        if ($user->isIsGroupAdmin() || $user->isIsAdmin()) {
            $groups = $groupRepository->groupsData();
            $query = $groupRepository->getGroupPagination();
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        } else {
            $userId = $this->getUser()->getDriver();
            $groups = $groupRepository->findGroupsByDriverId($userId);
            $query = $groupRepository->getDriversGroupPagination($userId);
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        }

        return $this->render('groups/main.html.twig', [
            'groups' => $groups,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/grupos/filtrados', name: 'group_filter')]
    public function filterIndex(GroupRepository $groupRepository, PaginatorInterface $paginator, Request $request):Response{

        $groups = $groupRepository->findGroupsByDriverId($this->getUser()->getDriver());
        $query = $groupRepository->getDriversGroupPagination($this->getUser()->getDriver());
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('groups/main.html.twig',
            [
                'groups' => $groups,
                'pagination' => $pagination,
            ]);
    }
    #[Route('/grupo/nuevo', name: 'group_new')]
    public function nuevo(GroupRepository $groupRepository, Request $request): Response
    {
        $group = new Group();
        $groupRepository->add($group);
        return $this->modificar($group, $groupRepository, $request);
    }
    #[Route('/grupos/buscar', name: 'group_search')]
    public function search(GroupRepository $groupRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $origin = $request->query->get('origin');
        $destination = $request->query->get('destination');

        if (empty($origin) || empty($destination)) {
            $this->addFlash('error', 'Los campos de origen y destino no pueden estar vacíos.');
            return $this->redirectToRoute('main');
        }

        $query = $groupRepository->findByOriginAndDestinationPaginated($origin, $destination);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('groups/filter.html.twig', [
            'groups' => $pagination->getItems(),
            'pagination' => $pagination,
            'origin' => $origin,
            'destination' => $destination,
        ]);
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
        return $this->render('groups/modificar.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
        ]);
    }
    #[Route('/grupo/eliminar/{id}', name: 'group_delete', methods: 'delete')]
    public function delete(Request $request, Group $group, GroupRepository $groupRepository): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {

            //$entityManager = $this->getDoctrine()->getManager();
            //$entityManager->remove($group);
            //$entityManager->flush();
            $groupRepository->remove($group);

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}