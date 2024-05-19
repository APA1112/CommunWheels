<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\User;
use App\Form\DriverType;
use App\Repository\DriverRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class DriverController extends AbstractController
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/conductores', name: 'driver_main')]
    public function index(DriverRepository $driverRepository, PaginatorInterface $paginator, Request $request): Response{
        $drivers = $driverRepository->findAllDrivers();
        $query = $driverRepository->getDriverPagination();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('users/main.html.twig', [
            'drivers' => $drivers,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/conductores/nuevo', name: 'driver_new')]
    public function nuevo(DriverRepository $driverRepository, Request $request, UserRepository $userRepository): Response
    {
        $driver = new Driver();
        $user = new User();
        $user->setDriver($driver);
        $driver->getUser()->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $driver->getUser()->setIsAdmin(0);
        $driver->getUser()->setIsDriver(1);
        $driver->getUser()->setIsGroupAdmin(0);
        $driver->setDaysDriven(0);
        $driverRepository->add($driver);
        $userRepository->add($user);
        return $this->modificar($driver, $driverRepository, $request);
    }

    #[Route('/conductor/modificar/{id}', name: 'driver_mod')]
    public function modificar(Driver $driver, DriverRepository $driverRepository, Request $request):Response
    {
        $form = $this->createForm(DriverType::class, $driver);

        $form->handleRequest($request);

        $nuevo = $driver->getId()===null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $username = $driver->getUser()->generateUsername($driver->getName(), $driver->getLastName());
                $driver->getUser()->setUsername($username);
                $driverRepository->save();
                if ($nuevo) {
                    $this->addFlash('success', 'Conductor creado con exito');
                } else {
                    $this->addFlash('success', 'Cambios guardados con exito');
                }
                return $this->redirectToRoute('driver_main');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('users/modificar.html.twig', [
            'form' => $form->createView(),
            'driver' => $driver,
        ]);
    }
    #[Route('/conductor/eliminar/{id}', name: 'driver_delete')]
    public function eliminar(Request $request, Driver $driver): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            // Eliminar el grupo (adaptar según tu lógica de eliminación)
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($driver);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}