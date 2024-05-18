<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NonSchoolDayController extends AbstractController
{
    #[Route('/nonschoolday', name: 'app_non_school_day')]
    public function index(): Response
    {
        return $this->render('non_school_day/index.html.twig', [
            'controller_name' => 'NonSchoolDayController',
        ]);
    }
}
