<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\BaseEntity;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

class ContactEntityService
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var AclProvider */
    private $aclProvider;

    public function __construct(\Swift_Mailer $mailer, AclProvider $aclProvider)
    {
        $this->mailer = $mailer;
        $this->aclProvider = $aclProvider;
    }

    /** @param BaseEntity&OwnableInterface $entity */
    public function emailEntity(BaseEntity $entity, string $from_name, string $from_email, string $subject, string $message): void
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

    /** @return array<string> */
    private function findRecipients(OwnableInterface $entity)
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

    /** @return array<\Acts\CamdramSecurityBundle\Entity\User> */
    private function findRecipientUsers(OwnableInterface $entity)
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
