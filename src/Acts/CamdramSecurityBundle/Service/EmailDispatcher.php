<?php
namespace Acts\CamdramSecurityBundle\Service;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Service\EmailConfirmationTokenGenerator;
use Symfony\Component\Routing\RouterInterface;

class EmailDispatcher
{
    private $mailer;
    private $twig;
    private $from_address;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $from_address)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from_address = $from_address;
    }

    public function sendRegistrationEmail(User $user, $token)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Welcome to Camdram')
            ->setFrom($this->from_address)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:create_account.txt.twig',
                    array(
                        'user' => $user,
                        'email_confirmation_token' => $token
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function resendEmailVerifyEmail(User $user, $token)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Verify your email address')
            ->setFrom($this->from_address)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:resend_email_verification.txt.twig',
                    array(
                        'user'                     => $user,
                        'email_confirmation_token' => $token
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function sendEmailVerifyEmail(User $user, $token)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Verify your new email address')
            ->setFrom($this->from_address)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:change_email.txt.twig',
                    array(
                        'user' => $user,
                        'email_confirmation_token' => $token
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function sendPasswordResetEmail(User $user, $token)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Password reset')
            ->setFrom($this->from_address)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:password_reset.txt.twig',
                    array(
                        'user' => $user,
                        'token' => $token
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}