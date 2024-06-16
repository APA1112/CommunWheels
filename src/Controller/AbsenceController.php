<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Driver;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\DriverRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted('ROLE_DRIVER')]
class AbsenceController extends AbstractController
{
    private $security;

    public function __construct(Security $security, MailerInterface $mailer)
    {
        $this->security = $security;
        $this->mailer = $mailer;
    }
    #[Route('/notificaciones', name: 'notify_main')]
    public function index(AbsenceRepository $absenceRepository, PaginatorInterface $paginator, DriverRepository $driverRepository, Request $request): Response
    {
        $user = $this->getUser();
        if($user->isIsGroupAdmin() || $user->isIsAdmin()){
            $absences = $absenceRepository->findAllAbsences();
            $query = $absenceRepository->absencesPagination();
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        } else {
            $driver = $user->getDriver();
            $absences = $absenceRepository->findDriverAbsences($driver);
            $query = $absenceRepository->driverAbsencesPagination($driver);
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        }

        return $this->render('notify/main.html.twig', [
            'absences' => $absences,
            'pagination' => $pagination,
        ]);
    }
    #[Route('/ausencias/filtrados', name: 'absence_filter')]
    public function filterIndex(AbsenceRepository $absenceRepository, PaginatorInterface $paginator, Request $request):Response{

        $driver = $this->getUser()->getDriver();
        $absences = $absenceRepository->findDriverAbsences($driver);
        $query = $absenceRepository->driverAbsencesPagination($driver);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('notify/main.html.twig',
            [
                'absences' => $absences,
                'pagination' => $pagination,
            ]);
    }

    #[Route('/notificacion/nueva/{id}', name: 'new_absence')]
    public function new(Driver $driver, AbsenceRepository $absenceRepository, Request $request, UserRepository $userRepository): Response
    {
        $absence = new Absence();
        $absence->setDriver($driver);
        $absenceRepository->add($absence);

        return $this->modificar($absence, $absenceRepository, $request, $userRepository);
    }

    #[Route('/notificacion/modificar/{id}', name: 'mod_absence')]
    public function modificar(Absence $absence, AbsenceRepository $absenceRepository, Request $request, UserRepository $userRepository):Response
    {
        $form = $this->createForm(AbsenceType::class, $absence);

        $form->handleRequest($request);

        $driver = $this->security->getUser()->getDriver();

        $absence->setDriver($driver);

        $nuevo = $absence->getId()===null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $absenceRepository->save();
                $admins = $userRepository->findGroupAdmins();

                foreach ($admins as $admin) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('commun.wheels@gmail.com', 'CommunWheels'))
                        ->to($admin->getDriver()->getEmail())
                        ->subject('Notificación de ausencia')
                        ->htmlTemplate('emails/absence_notification.html.twig')
                        ->context([
                            'group_admin_name' => $admin->getDriver()->getName(),
                            'driver_name' => $absence->getDriver()->getName() . ' '. $absence->getDriver()->getLastName(),
                            'driver_groups' => $absence->getDriver()->getGroupCollection(),
                            'start_date' => $absence->getAbsenceDate()->format('d-m-Y'),
                            'reason' => $absence->getDescription(),
                            'support_email' => 'support@communwheels.com',
                            'year' => date('Y')
                        ]);

                    $this->mailer->send($email);
                }

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