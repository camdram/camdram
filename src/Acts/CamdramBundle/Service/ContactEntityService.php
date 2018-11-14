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

        $message = (new \Swift_Message('[Camdram] ' . $subject))
            ->setFrom($from_email, $from_name)
            ->setTo($recipients)
            ->setBody($message)
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

        if ($entity instanceof Show) {
            if (count($recipients) == 0 && $entity->getSociety()) {
                $recipients = $this->aclProvider->getOwners($entity->getSociety());
            }
            if (count($recipients) == 0 && $entity->getVenue()) {
                $recipients = $this->aclProvider->getOwners($entity->getVenue());
            }
        }

        return $recipients;
    }
}
