<?php

namespace App\Controller;

use App\Entity\TimeTable;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/trip/{id}', name: 'app_trip')]
    public function index(TimeTable $timeTable, TripRepository $tripRepository, Request $request): Response
    {
        $trips = $tripRepository->findByTimeTable($timeTable->getId());
        $groupId = $timeTable->getBand()->getId();

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
            'group' => $groupId
        ]);
    }
}
