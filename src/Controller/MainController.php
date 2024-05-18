<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->render('main/index.html.twig');
        }else{
            return $this->redirectToRoute('app_login');
        }

    }
}