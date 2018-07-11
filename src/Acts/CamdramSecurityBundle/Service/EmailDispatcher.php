<?php

namespace Acts\CamdramSecurityBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramBundle\Entity\Show;

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

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, \Twig_Environment $twig, $adminEmail)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from_address = $adminEmail;
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
     * resource (show, society, or venue).
     */
    public function sendAceEmail(AccessControlEntry $ace)
    {
        $message = \Swift_Message::newInstance()
            ->setFrom($this->from_address)
            ->setTo($ace->getUser()->getEmail());
        /* Get the resource and pass it to the template. */
        if ($ace->getType() == 'show') {
            $show = $this->em->getRepository('ActsCamdramBundle:Show')->findOneById($ace->getEntityId());
            $message->setSubject('Access to show '.$show->getName().' on Camdram granted')
                ->setBody(
                    $this->twig->render(
                        'email/ace.txt.twig',
                        array(
                            'is_pending' => false, //$is_pending,
                            'ace' => $ace,
                            'entity' => $show
                        )
                    )
                );
        }
        $this->mailer->send($message);
    }

    /**
     * Send an email informing someone that they've been granted access to a
     * resource (show, society, or venue).
     */
    public function sendPendingAceEmail(PendingAccess $ace)
    {
        $message = \Swift_Message::newInstance()
            ->setFrom($this->from_address)
            ->setTo($ace->getEmail());
        /* Get the resource and pass it to the template. */
        switch ($ace->getType()) {
            case 'show':
                $entity = $this->em->getRepository('ActsCamdramBundle:Show')->findOneById($ace->getRid());
                break;
            case 'society':
                $entity = $this->em->getRepository('ActsCamdramBundle:Society')->findOneById($ace->getRid());
                break;
            case 'venue':
                $entity = $this->em->getRepository('ActsCamdramBundle:Venue')->findOneById($ace->getRid());
                break;
        }
        $message->setSubject('Access to '.$ace->getType().' '.$entity->getName().' on Camdram granted')
            ->setBody(
                $this->twig->render(
                    'email/ace.txt.twig',
                    array(
                        'is_pending' => true,
                        'ace' => $ace,
                        'entity' => $entity
                    )
                )
            );
        $this->mailer->send($message);
    }

    /**
     * Request administrator privileges for a show
     */
    public function sendShowAdminReqEmail(AccessControlEntry $ace)
    {
        $show = $this->em->getRepository('ActsCamdramBundle:Show')->findOneById($ace->getEntityId());
        $owners = $this->em->getRepository('ActsCamdramSecurityBundle:User')
                    ->getEntityOwners($show);
        $emails = array();
        foreach ($owners as $user) {
            $emails[$user->getEmail()] = $user->getName();
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Show access request on Camdram: '.$show->getName())
            ->setFrom($this->from_address)
            ->setTo($emails)
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_access_requested.txt.twig',
                    array(
                        'ace' => $ace,
                        'show' => $show
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}
