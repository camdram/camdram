<?php
namespace Acts\CamdramSecurityBundle\Service;

use Doctrine\ORM\EntityManager;
use Acts\CamdramSecurityBundle\Entity\User,
    Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Service\EmailConfirmationTokenGenerator;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class for constructing and sending emails. Emails are typically sent as a 
 * result of an event occurring, such as a user changing their email address.
 */
class EmailDispatcher
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $mailer;
    private $twig;
    private $from_address;

    public function __construct(EntityManager $em, \Swift_Mailer $mailer, \Twig_Environment $twig, $from_address)
    {
        $this->em = $em;
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
    
    /**
     * Send an email informing someone that they've been granted access to a
     * resource (show, society, or venue), pending creating a new account on Camdram.
     */
    public function sendPendingAceEmail(PendingAccess $ace)
    {
        $message = \Swift_Message::newInstance()
            ->setFrom($this->from_address)
            ->setTo($ace->getEmail());
        /* Get the resource and pass it to the template. */
        if ($ace->getType() == 'show')
        {
            $show = $this->em->getRepository('ActsCamdramBundle:Show')->findOneById($ace->getRid());
            $message->setSubject('Access to show '.$show->getName().'on Camdram granted')
                ->setBody(
                    $this->twig->render(
                        'ActsCamdramBundle:Email:pending_ace.txt.twig',
                        array(
                            'is_pending' => true,
                            'ace' => $ace,
                            'entity' => $show
                        )
                    )
                );
        }
        $this->mailer->send($message);
    }
}

