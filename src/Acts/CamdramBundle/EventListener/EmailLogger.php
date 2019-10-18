<?php
namespace Acts\CamdramBundle\EventListener;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Acts\CamdramSecurityBundle\Entity\User;

class EmailLogger implements Swift_Events_SendListener
{
    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * EmailLogger constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        RequestStack $requestStack,
            TokenStorageInterface $tokenStorage
    ) {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $event)
    {
    }

    public function sendPerformed(Swift_Events_SendEvent $event)
    {
        $level = $this->getLogLevel($event);
        $message = $event->getMessage();

        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;
        if ($user instanceof User) {
            $userInfo = ['id' => $user->getId(), 'name' => $user->getName()];
        } else {
            $userInfo = null;
        }

        $request = $this->requestStack->getCurrentRequest();
        $ip = $request ? $request->getClientIp() : null;

        $this->logger->log(
            $level,
            $message->getSubject().' - '.$message->getId(),
            [
                'result'  => $this->getResultString($event),
                'sender_ip' => $ip,
                'user'    => $userInfo,
                'subject' => $message->getSubject(),
                'to'      => $message->getTo(),
                'cc'      => $message->getCc(),
                'bcc'     => $message->getBcc(),
            ]
        );
    }

    private function getResultString(Swift_Events_SendEvent $event)
    {
        switch ($event->getResult()) {
            // Sending has yet to occur
            case Swift_Events_SendEvent::RESULT_PENDING:
                return "Pending";
                // Email is spooled, ready to be sent
            case Swift_Events_SendEvent::RESULT_SPOOLED:
                return "Spooled";
                // Sending failed
            case Swift_Events_SendEvent::RESULT_FAILED:
                return "Failed";
                // Sending worked, but there were some failures
            case Swift_Events_SendEvent::RESULT_TENTATIVE:
                return "Tentative";
                // Sending was successful
            case Swift_Events_SendEvent::RESULT_SUCCESS:
                return "Success";
            default:
                return $event->getResult();
        }
    }

    /**
     * @param Swift_Events_SendEvent $evt
     *
     * @return string
     */
    private function getLogLevel(Swift_Events_SendEvent $event)
    {
        switch ($event->getResult()) {
            // Sending has yet to occur
            case Swift_Events_SendEvent::RESULT_PENDING:
                return LogLevel::INFO;
            // Email is spooled, ready to be sent
            case Swift_Events_SendEvent::RESULT_SPOOLED:
                return LogLevel::INFO;
            // Sending failed
            case Swift_Events_SendEvent::RESULT_FAILED:
                return LogLevel::CRITICAL;
            // Sending worked, but there were some failures
            case Swift_Events_SendEvent::RESULT_TENTATIVE:
                return LogLevel::ERROR;
            // Sending was successful
            case Swift_Events_SendEvent::RESULT_SUCCESS:
                return LogLevel::INFO;
            default:
                return LogLevel::CRITICAL;
        }
    }
}
