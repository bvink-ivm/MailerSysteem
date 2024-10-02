<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = new User();
        $user->setEmail('kbivminfo@gmail.com');
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return $this->render('register/register.html.twig');
    }
}
