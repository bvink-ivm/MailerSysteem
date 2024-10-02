<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function requestLoginLink(LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, MailerInterface $mailer): Response
    {      
            

        // if it's not submitted, render the form to request the "login link"
        return $this->render('security/request_login_link.html.twig');
    }

    #[Route('/link', name: 'link')]
    public function link(LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // load the user in some way (e.g. using the form input)
        $email = $data['email'];
        $user = $userRepository->findOneBy(['email' => $email]);

        // create a login link for $user this returns an instance
        // of LoginLinkDetails
        if ($user){   
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $loginLink = $loginLinkDetails->getUrl();
            $transport = Transport::fromDsn('smtp://kbivminfo@gmail.com:xmbjpnceamtyxych@smtp.gmail.com:587');
            $mailer = new Mailer($transport);
            //return $this->redirectToRoute('link');
            $email = (new Email())
            ->from('kbivminfo@gmail.com')
            ->to($email)
            ->subject('test subject')
            ->html('<p>Je in </p?
                        <a href=" '. $loginLink .'">loginLink</a>
                    <h1 style="color: yellow ; background-color:blue ; padding:16px;"> this is a h1 </h1>');

            // Send the email
            try {
                $mailer->send($email);
                return new Response('Email sent successfully!');
            } catch (TransportExceptionInterface $e) {
                return new Response('Could not send email: ' . $e->getMessage());
            }
            
            return new JsonResponse(['status' => 'success']);
        }
        else{
            return new JsonResponse(['status' => 'failure',
            'message' => 'Does not exist'
            ]);
        }
    }
}
