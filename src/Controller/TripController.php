<?php

namespace App\Controller;

use App\Entity\TimeTable;
use App\Entity\Trip;
use App\Repository\DriverRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
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
        $notNedeed = false;
        $dateTrip = $trip->getTripDate()->format('Y-m-d');
        $driverAbsences = [];

        foreach ($actualDriverAbsences as $driverAbsence) {
            $driverAbsences[] = $driverAbsence->getAbsenceDate()->format('Y-m-d');
        }
        foreach ($driverAbsences as $driverAbsence) {
            if ($driverAbsence !== $dateTrip) {
                $notNedeed = false;
            } else {
                $notNedeed = true;
                break;
            }
        }

        if (count($trip->getPassengers()) == 0 and $notNedeed) {
            $tripRepository->remove($trip);
            $entityManager->flush();
            /**
             * Enviar email cuando se elimine el trip y hacer la plantilla
             *
             * SweetAlert cuando se elimine
             */
            return $this->render('trip/mod.html.twig', [
                'drivers' => $drivers,
                'timeTable' => $timeTable,
                'trip' => $trip,
                'notNeeded' => $notNedeed
            ]);
        } else {
            $drivers = $trip->getPassengers();
            $tripDate = $trip->getTripDate();
            $availableDriver = null;

            foreach ($drivers as $driver) {
                $driverAbsences = $driver->getAbsences();
                $isAvailable = true;

                foreach ($driverAbsences as $driverAbsence) {
                    if ($driverAbsence->getAbsenceDate() == $tripDate) {
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
                /**
                 * Enviar email cuando se modifique el trip y hacer la plantilla
                 *
                 * SweetAlert cuando se modifique
                */
                return $this->redirectToRoute('see_timetable', ['id' => $timeTable->getId()]);
            }
        }

        return $this->render('trip/mod.html.twig', [
            'drivers' => $drivers,
            'timeTable' => $timeTable,
            'trip' => $trip,
            'notNeeded' => $notNedeed
        ]);
    }


    #[IsGranted('ROLE_GROUP_ADMIN')]
    #[Route('/cuadrante/eliminar/{id}', name: 'trip_delete')]
    public function eliminar(Request $request, Trip $trip, TripRepository $tripRepository): JsonResponse
    {
        // Verifica que la solicitud es AJAX
        if ($request->isXmlHttpRequest()) {
            $tripRepository->remove($trip);

            return new JsonResponse(['status' => 'Trip deleted'], 200);
        }

        return new JsonResponse(['status' => 'Invalid request'], 400);
    }
}
