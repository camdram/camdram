<?php

namespace Acts\CamdramBackendBundle\Command;

use Acts\CamdramAdminBundle\Entity\Support;
use Acts\CamdramBackendBundle\Entity\EmailBounce;
use Acts\CamdramBackendBundle\Service\EmailParser;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Mail\Header\From;
use Zend\Mail\Header\To;

class IssuesEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:issues:email')
            ->setDescription('Process message sent to support email address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $raw = '';
        while ($line = fgets(STDIN)) {
            $raw .= $line;
        }

        $parser = new EmailParser($raw);

        $issue = new Support();
        $issue->setFrom($parser->getRawFrom())
            ->setTo($parser->getRawTo())
            ->setCc($parser->getRawCc())
            ->setSubject($parser->getSubject())
            ->setBody($parser->getTextPart())
        ;

        if (preg_match('/^support-(?:Lreply-)?([0-9]+)@/', $issue->getTo(), $matches)) {
            $this->processReply($issue, $matches[1], true, $output);
        } elseif (preg_match('/^support-(?:reply-)?([0-9]+)@/', $issue->getCc(), $matches)) {
            $this->processReply($issue, $matches[1], false, $output);
        } elseif (preg_match('/^support-bounces@/', $issue->getTo())) {
            $this->processBounce($parser->getRawFrom(), $parser->getRawTo(), $parser->getSubject(), $parser->getRawBody(), $output);
        } else {  //support@ or websupport@
            $this->processNew($issue, $output);
        }
    }

    protected function getRepo()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function processNew(Support $issue, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($issue);
        $em->flush();

        $this->forwardEmail($issue, $output);
    }

    protected function processReply(Support $issue, $issue_id, $forwardMessage, $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('ActsCamdramAdminBundle:Support');
        if (($orig = $repo->findOneById($issue_id)) !== null) {
            $issue->setParent($orig);
            //Reopen issue if it's been closed
            if ($issue->getOriginal()->getOwner()) {
                $issue->getOriginal()->setState(Support::STATE_ASSIGNED);
            } else {
                $issue->getOriginal()->setState(Support::STATE_UNASSIGNED);
            }

            $em->persist($issue);
            $em->flush();

            if ($forwardMessage) {
                $this->forwardEmail($issue, $output);
            }
        }
    }

    protected function forwardEmail(Support $issue, OutputInterface $output)
    {
        $from_email = 'support-'.$issue->getOriginalId().'@camdram.net';
        $sender = From::fromString('From:'.$issue->getFrom())->getAddressList()->current();

        $mailer = $this->getContainer()->get('mailer');

        foreach ($this->getRecipients($issue) as $email => $name) {
            $output->writeln('Sending to '.$name.' ('.$email.')');

            $message = \Swift_Message::newInstance();
            $message->setSubject($issue->getSubject())
                ->setTo($email, $name)
                ->setSender($sender->getEmail(), $sender->getName())
                ->setFrom($from_email, 'Cammdram Support')
                ->setReplyTo($from_email, 'Camdram Support')
                ->setReturnPath('support-bounces@camdram.net')
                ->setBody($issue->getBody())
            ;
            $mailer->send($message);
        }
    }

    protected function getRecipients(Support $issue)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('ActsCamdramSecurityBundle:User');
        $issueFrom = From::fromString('From:'.$issue->getFrom())->getAddressList()->current();

        $recipients = array();
        if ($issue->getOriginal()->getOwner()) {
            //The issue has an owner, so just include the issue owner
            $owner = $issue->getOriginal()->getOwner();
            if ($issueFrom->getEmail() != $owner->getFullEmail()) {
                $recipients[$owner->getFullEmail()] = $owner->getName();
            }
        } else {
            //No owner, so include all admins
            $admins = $repo->findAdmins(AccessControlEntry::LEVEL_FULL_ADMIN);
            foreach ($admins as $admin) {
                if ($issueFrom->getEmail() != $admin->getFullEmail()) {
                    $recipients[$admin->getFullEmail()] = $admin->getName();
                }
            }
        }

        //Also include the original sender and receiver
        $from = From::fromString('From:'.$issue->getOriginal()->getFrom());
        $to = To::fromString('To:'.$issue->getOriginal()->getTo());
        foreach (array($from, $to) as $email) {
            /** @var $email \Zend\Mail\Header\AbstractAddressList */
            foreach ($email->getAddressList() as $address) {
                if (strpos($address->getEmail(), '@camdram.net') === false
                        && $address->getEmail() != $issueFrom->getEmail()) {
                    $recipients[$address->getEmail()] = $address->getName();
                }
            }
        }

        return $recipients;
    }

    protected function processBounce($from, $to, $subject, $body, OutputInterface $output)
    {
        $bounce = new EmailBounce();
        $bounce->setFromHeader($from);
        $bounce->setToHeader($to);
        $bounce->setSubject($subject);
        $bounce->setBody($body);
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($bounce);
        $em->flush();
    }
}
