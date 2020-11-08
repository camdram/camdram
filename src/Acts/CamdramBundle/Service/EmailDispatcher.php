<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;

class EmailDispatcher
{
    private $mailer;
    private $twig;
    private $from_address;

    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $twig, $adminEmail)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from_address = $adminEmail;
    }

    private function emailArrayFromUsers(array $users)
    {
        $emails = array();
        foreach ($users as $user) {
            $emails[$user->getEmail()] = $user->getName();
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
        foreach ($bccEmails as $email => $name) {
            if (isset($toEmails[$email])) {
                unset($bccEmails[$email]);
            }
        }

        $message =  (new \Swift_Message('New show needs authorization on Camdram: '.$show->getName()))
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($toEmails)
            ->setBcc($bccEmails)
            /* HTML */
            ->setBody(
                $this->twig->render(
                    'email/show_created.html.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                ),
                'text/html'
            )

/* Plain Text */
            ->addPart(
                $this->twig->render(
                    'email/show_created.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                ),
                'text/plain'
            )
        ;
        $this->mailer->send($message);
    }

    public function sendShowApprovedEmail(Show $show, array $owners, User $authorisedBy)
    {
        $emails = array();
        foreach ($owners as $user) {
            $emails[$user->getEmail()] = $user->getName();
        }

        $message = (new \Swift_Message('Show authorised on Camdram: '.$show->getName()))
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($emails)
            ->setBody(
                $this->twig->render(
                    'email/show_approved.txt.twig',
                    array(
                        'show' => $show,
                        'authorisedBy' => $authorisedBy,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    public function sendContactUsEmail($from, $name, $subject, $message)
    {
        $body = "From: $name <$from>\n\n".$message;

        $message = (new \Swift_Message($subject))
            ->setFrom([$this->from_address => 'camdram.net Contact Form'])
            ->setReplyTo($from, $name)
            ->setTo($this->from_address)
            ->setBody($body)
        ;
        $this->mailer->send($message);
    }

    public function sendShowSocietyChangedEmail(Show $show, array $owners, array $moderators)
    {
        $toEmails = $this->emailArrayFromUsers($moderators);

        $message = (new \Swift_Message('Authorized society(s) changed to '. implode("; ",
               $show->getSocieties()->map(function($s) { return $s->getName(); })->toArray())))
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($toEmails)
            ->setBody(
                $this->twig->render(
                    'email/show_society_changed.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }

    /**
     * Email sent after the venue(s) for a show change.
     */
    public function sendShowVenueChangedEmail(Show $show, array $owners, array $moderators,
        array $added, array $removed): void
    {
        $toEmails = $this->emailArrayFromUsers($moderators);

        $message = (new \Swift_Message('Venue(s) changed for '.$show->getName()))
            ->setFrom(array($this->from_address => 'camdram.net'))
            ->setTo($toEmails)
            ->setBody(
                $this->twig->render(
                    'email/show_venue_changed.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                        'added' => $added,
                        'removed' => $removed,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}
