<?php

namespace App\Controller;

use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;

class TripController extends AbstractController
{
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    #[Route('/trip/confirm/{id}', name: 'confirm_trip')]
    public function confirmTrip(Trip $trip, DriverRepository $driverRepository, TripRepository $tripRepository): Response
    {
        $trip = $tripRepository->find($trip->getId());
        if (!$trip) {
            throw $this->createNotFoundException('No se ha encontrado viaje para el id ' . $trip->getId());
        }

        // Verificar que el usuario actual es el conductor del viaje
        if ($trip->getDriver() !== $this->getUser()->getDriver()) {
            throw $this->createAccessDeniedException('No eres el conductor de este viaje.');
        }

        // Incrementar daysDriven
        $driver = $trip->getDriver();
        $driver->setDaysDriven($driver->getDaysDriven() + 1);

        $trip->setActive(false);

        $driverRepository->save();
        //dd($trip->getTimeTable()->getBand());

        return $this->render('trip/new.html.twig', [
            'timeTable' => $trip->getTimeTable(),
            'trips' => $trip->getTimeTable()->getTrips(),
            'group' => $trip->getTimeTable()->getBand(),
        ]);
    }

    #[Route('/trip/{id}', name: 'mod_trip')]
    public function modTrip(Trip $trip, TripRepository $tripRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $actualDriverAbsences = $trip->getDriver()->getAbsences();
        $drivers = $trip->getPassengers();
        $timeTable = $trip->getTimeTable();
        $dateTrip = $trip->getTripDate()->format('Y-m-d');
        $driverAbsences = [];

        foreach ($actualDriverAbsences as $driverAbsence) {
            $driverAbsences[] = $driverAbsence->getAbsenceDate()->format('Y-m-d');
        }

        $notNedeed = in_array($dateTrip, $driverAbsences);
        $noPassengers = count($drivers) == 0;

        if (!$notNedeed){
            return $this->render('trip/mod.html.twig', [
                'drivers' => $drivers,
                'timeTable' => $timeTable,
                'trip' => $trip,
                'notNeeded' => $notNedeed,
            ]);
        }

        if ($noPassengers) {
            $tripRepository->remove($trip);
            $entityManager->flush();

            foreach ($timeTable->getBand()->getDrivers() as $driver) {
                // Enviar correo al nuevo conductor
                $email = (new TemplatedEmail())
                    ->from(new Address('commun.wheels@gmail.com', 'CommunWheels'))
                    ->to($driver->getEmail())
                    ->subject('Modificacion en el cuadrante del grupo ' . $timeTable->getBand())
                    ->htmlTemplate('emails/trip_mod_notification.html.twig')
                    ->context([
                        'driver_name' => $driver->getName(),
                        'trip_date' => $trip->getTripDate()->format('d-m-Y'),
                        'group' => $timeTable->getBand()->getName(),
                        'support_email' => 'commun.wheels@gmail.com',
                        'year' => date('Y')
                    ]);

                $this->mailer->send($email);

            }

            return $this->redirectToRoute('see_timetable', ['id' => $timeTable->getId(), 'delete' => 1]);
        }

        $availableDriver = null;
        foreach ($drivers as $driver) {
            $isAvailable = true;
            foreach ($driver->getAbsences() as $driverAbsence) {
                if ($driverAbsence->getAbsenceDate() == $trip->getTripDate()) {
                    $isAvailable = false;
                    break;
                }
            }
            if ($isAvailable) {
                $availableDriver = $driver;
                break;
            }
        }

        if ($availableDriver !== null) {
            $trip->setDriver($availableDriver);
            $trip->removePassenger($availableDriver);
            $entityManager->persist($trip);
            $entityManager->flush();

            foreach ($timeTable->getBand()->getDrivers() as $driver) {
                // Enviar correo al nuevo conductor
                $email = (new TemplatedEmail())
                    ->from(new Address('commun.wheels@gmail.com', 'CommunWheels'))
                    ->to($driver->getEmail())
                    ->subject('Modificacion en el cuadrante del grupo ' . $timeTable->getBand())
                    ->htmlTemplate('emails/trip_mod_notification.html.twig')
                    ->context([
                        'driver_name' => $driver->getName(),
                        'trip_date' => $trip->getTripDate()->format('d-m-Y'),
                        'group' => $timeTable->getBand()->getName(),
                        'support_email' => 'commun.wheels@gmail.com',
                        'year' => date('Y')
                    ]);

                $this->mailer->send($email);

            }

        }
        return $this->redirectToRoute('see_timetable', ['id' => $timeTable->getId(), 'modify' => 1]);
    }
}
