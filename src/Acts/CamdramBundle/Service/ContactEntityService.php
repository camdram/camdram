<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

class ContactEntityService
{
    private $mailer;

    private $aclProvider;

    public function __construct(\Swift_Mailer $mailer, AclProvider $aclProvider)
    {
        $this->mailer = $mailer;
        $this->aclProvider = $aclProvider;
    }

    public function emailEntity($entity, $from_name, $from_email, $subject, $message)
    {
        $recipients = $this->findRecipients($entity);
        $msg = "You are receiving this email because you manage ".$entity->getName()." on Camdram.\n\n"
            ."From: $from_name <$from_email>\n\n"
            .$message;

        $message = (new \Swift_Message('[Camdram] ' . $subject))
            ->setFrom($from_email, $from_name)
            ->setTo($recipients)
            ->setBody($msg)
            ;

        $this->mailer->send($message);
    }

    private function findRecipients($entity)
    {
        $users = $this->findRecipientUsers($entity);
        $emails = array();

        foreach ($users as $user) {
            if ($user->getEmail() && $user->getIsEmailVerified()) {
                $emails[$user->getEmail()] = $user->getName();
            }
        }

        if (count($emails) == 0) {
            $emails = array('support@camdram.net');
        }

        return $emails;
    }

    private function findRecipientUsers($entity)
    {
        $recipients = $this->aclProvider->getOwners($entity);

        if ($entity instanceof Show && count($recipients) == 0) {
            $recipients = $this->aclProvider->getOwnersOfOwningSocs($entity);

            if (count($recipients) == 0) {
                $recipients = $this->aclProvider->getOwnersOfOwningVens($entity);
            }
        }

        return $recipients;
    }
}
