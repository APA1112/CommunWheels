<?php

namespace App\Controller;

use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NonSchoolDayController extends AbstractController
{
    #[Route('/nonschoolday/{id}', name: 'app_non_school_day')]
    public function index(Group $group): Response
    {
        return $this->render('non_school_day/index.html.twig', [
            'group'=>$group->getId()
        ]);
    }
}
