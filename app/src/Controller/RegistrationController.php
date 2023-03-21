<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use App\Services\CustomMailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, NotifierInterface $notifier, CustomMailerService $customMailerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $notifier->send(new Notification('Thank you for the feedback; your comment will be posted after moderation.', ['browser']));
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles([0]);
            $hash = md5($user->getEmail());
            $user->setHash($hash);
            $entityManager->persist($user);
            $entityManager->flush();
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/activation/' . $hash;
            $customMailerService->sendMail($user->getEmail(), 'Dear customer', 'Активация аккаунта STROY-BEL',
            "<h1>Активация аккаунта STROY-BEL</h1><p>Уважаемый клиент, для активации аккаунта перейдите по следующей ссылке: </p><a href=\"$url\">Активация</a>");

            // do anything else you need here, like send an email
            if (!$user->isActivated()) {
                return $this->redirectToRoute('registrated');
            }
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
