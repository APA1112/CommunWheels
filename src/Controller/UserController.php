<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\User;
use App\Form\ChangeUserPasswordType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/user', name: 'user_panel')]
    public function panel():Response
    {
        return $this->render('users/panel.html.twig');
    }
    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository
    ): Response
    {
        // Obtener usuario actual
        $user = $this->getUser();
        // Crear formulario con el usuario actual. admin es false por defecto
        $form = $this->createForm(ChangeUserPasswordType::class, $user);
        // Gestionar cambio de contraseña
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        // Codificar la contraseña del campo "newPassword", no mapeado
            $hashedPassword = $this->passwordHasher->hashPassword($user, $form->get('newPassword')->getData());
            $user->setPassword($hashedPassword);
            $userRepository->save();
            $this->addFlash('success', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('main');
        }
        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/change-password/{id}', name: 'app_change_user_password')]
    public function changeUserPassword(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository,
        Driver $driver
    ): Response
    {
        $user = $driver->getUser();
        // Denegar acceso si no es administrador
        $this->denyAccessUnlessGranted('ROLE_GROUP_ADMIN');
        // Crear formulario para el usuario pasado como parámetro
        $form = $this->createForm(ChangeUserPasswordType::class, $user, [
        // Si el usuario al que se quiere cambiar la contraseña somos
        // nosotros mismos, no podemos omitir preguntar la contraseña antigua
            'admin' => $user !== $this->getUser()
        ]);
        // Gestionar cambio de contraseña
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        // Codificar la contraseña del campo "newPassword", no mapeado
            $user->setPassword( // Cambiar el nombre del método según la propiedad que contenga la clave
                $passwordEncoder->hashPassword($user, $form->get('newPassword')->getData())
            );
            $userRepository->save();
            $this->addFlash('success', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('main');
        }
        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}