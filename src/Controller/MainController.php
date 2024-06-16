<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Security $security, TripRepository $tripRepository): Response
    {
        $trips = $tripRepository->findInactiveTrips();

        if ($security->getUser()) {
            return $this->render('main/index.html.twig', [
                'trips' => $trips,
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }
}