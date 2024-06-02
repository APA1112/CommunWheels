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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_DRIVER')]
class DriverController extends AbstractController
{
    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/conductores', name: 'driver_main')]
    public function index(DriverRepository $driverRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $drivers = $driverRepository->findAllDrivers();
        $query = $driverRepository->getDriverPagination();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );
        return $this->render('users/main.html.twig', [
            'drivers' => $drivers,
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/conductores/nuevo', name: 'driver_new')]
    public function nuevo(DriverRepository $driverRepository, Request $request, UserRepository $userRepository): Response
    {
        $driver = new Driver();
        $user = new User();
        $user->setDriver($driver);
        $plainPassword = 'cambiame';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $driver->getUser()->setPassword($hashedPassword);
        $driver->getUser()->setIsAdmin(0);
        $driver->getUser()->setIsDriver(1);
        $driver->setDaysDriven(0);

        $form = $this->createForm(DriverType::class, $driver);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $username = $driver->getUser()->generateUsername($driver->getName(), $driver->getLastName());
                $driver->getUser()->setUsername($username);
                $driver->getUser()->setIsGroupAdmin($form->get('isAdmin')->getData());

                $userRepository->add($user);
                $driverRepository->add($driver);

                // Enviar correo al nuevo conductor
                $email = (new Email())
                    ->from('commun.wheels@gmail.com')
                    ->to($driver->getEmail())
                    ->subject('Bienvenido a CommunWheels ' . $driver->getName())
                    ->html('
                        <p>Tu cuenta de conductor ha sido creada con las siguientes credenciales.</p>
                        <p>Usuario: ' . $user->getUsername() . '</p>
                        <p>Contraseña: ' . $plainPassword . '</p>
                        <p>Recuerda que debes cambiar tu contraseña en panel de usuario.</p>
                        <a href="https://communwheels.prodevnet.es">CommunWheels</a>');

                $this->mailer->send($email);

                $this->addFlash('success', 'Conductor creado con éxito');
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


    #[Route('/conductor/modificar/{id}', name: 'driver_mod')]
    public function modificar(Driver $driver, DriverRepository $driverRepository, Request $request): Response
    {
        $form = $this->createForm(DriverType::class, $driver);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $driver->getUser()->setIsGroupAdmin($form->get('isAdmin')->getData());
                $driverRepository->save();

                $this->addFlash('success', 'Cambios guardados con exito');

                return $this->redirect($request->request->get('redirect') ?? $this->generateUrl('driver_main'));
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('users/modificar.html.twig', [
            'form' => $form->createView(),
            'driver' => $driver,
        ]);
    }

    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/conductor/eliminar/{id}', name: 'driver_delete')]
    public function eliminar(Request $request, Driver $driver, DriverRepository $driverRepository): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            $driverRepository->remove($driver);

            return new JsonResponse(['status' => 'Group deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}
