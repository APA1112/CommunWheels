<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimeTableController extends AbstractController
{
    #[Route('/cuadrante', name: 'timetable_main')]
    public function index(): Response
    {
        return $this->render('TimeTable/main.html.twig', []);
    }
}