<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
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

    private function emailArrayFromUsers(array $users)
    {
        $emails = array();
        foreach ($users as $user) {
            $emails[$user->getFullEmail()] = $user->getName();
        }
        return $emails;
    }

    /**
     * Send an email to the relevant moderators when this show is created.
     */
    public function sendShowCreatedEmail(Show $show, array $owners, array $moderators, array $admins)
    {
        $toEmails = $this->emailArrayFromUsers($moderators);
        $bccEmails = $this->emailArrayFromUsers($admins);
        foreach ($bccEmails as $email => $name)
        {
            if (isset($emails[$email])) unset($bccEmails[$email]);
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('New show needs authorization on Camdram: '.$show->getName())
            ->setFrom(array($this->from_address => 'camdram.net'))
            
            /* HTML */
            ->setTo($toEmails)
            ->setBcc($bccEmails)
            
            /* HTML */
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_created.html.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                ), 'text/html'
            )
            
            /* Plain Text */
            ->addPart(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_created.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                ), 'text/plain'
            )
        ;
        $this->mailer->send($message);
    }

    public function sendShowApprovedEmail(Show $show, array $owners)
    {
        $emails = array();
        foreach ($owners as $user) {
            $emails[$user->getFullEmail()] = $user->getName();
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Show authorised on Camdram: '.$show->getName())
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($emails)
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_approved.txt.twig',
                    array(
                        'show' => $show,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function sendContactUsEmail($from, $subject, $message)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setReplyTo($from)
            ->setTo($this->from_address)
            ->setBody($message)
        ;
        $this->mailer->send($message);
    }

    public function sendShowSocietyChangedEmail(Show $show, array $owners, array $moderators)
    {
        $toEmails = $this->emailArrayFromUsers($moderators);

        $message = \Swift_Message::newInstance()
            ->setSubject('Society changed to '. $show->getSociety()->getName() .': '.$show->getName())
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($toEmails)
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_society_changed.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function sendShowVenueChangedEmail(Show $show, array $owners, array $moderators)
    {
        $toEmails = $this->emailArrayFromUsers($moderators);

        $message = \Swift_Message::newInstance()
            ->setSubject('Venue changed to '. $show->getVenue()->getName() .': '.$show->getName())
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($toEmails)
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_venue_changed.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}
