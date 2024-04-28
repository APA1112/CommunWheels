<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/entrar', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response{
        //Obtener el ultimo mensaje de error, si existe
        $error = $authenticationUtils->getLastAuthenticationError();

        //Obtenemos el ultimo nombre de usuario introducido
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', ['last_username'=>$lastUsername, 'error'=>$error]);
    }

    #[Route(path: '/salir', name: 'app_logout')]
    public function logout():void{
        throw new \LogicException('Esto no deberia ejecutarse nunca');
    }
}