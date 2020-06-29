<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Event\VenueChangeEvent;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use HWI\Bundle\OAuthBundle\HWIOAuthEvents;
use HWI\Bundle\OAuthBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * EmailSendListener
 *
 * Functions triggered by events generated by the Security Bundle. These functions
 * result in automated emails being sent by Camdram.
 */
class EmailSendListener implements EventSubscriberInterface
{
    private $dispatcher;
    private $generator;
    private $moderationManager;

    public function __construct(EmailDispatcher $dispatcher, TokenGenerator $generator,
            ModerationManager $moderationManager)
    {
        $this->dispatcher = $dispatcher;
        $this->generator = $generator;
        $this->moderationManager = $moderationManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            HWIOAuthEvents::REGISTRATION_COMPLETED => 'onRegistrationEvent',
            CamdramSecurityEvents::ACE_CREATED => 'onAceCreatedEvent',
            CamdramSecurityEvents::PENDING_ACCESS_CREATED => 'onPendingAccessCreatedEvent',
            CamdramSecurityEvents::VENUES_CHANGED => 'onVenuesChangedEvent',
        ];
    }

    public function onRegistrationEvent(FilterUserResponseEvent $event)
    {
        /** @var \Acts\CamdramSecurityBundle\Entity\User $user */
        $user = $event->getUser();
        if (!$user->getIsEmailVerified())
        {
            $token = $this->generator->generateEmailConfirmationToken($user);
            $this->dispatcher->sendRegistrationEmail($user, $token);
        }
    }

    public function onEmailChangeEvent(UserEvent $event)
    {
        $user = $event->getUser();
        $token = $this->generator->generateEmailConfirmationToken($user);

        $this->dispatcher->sendEmailVerifyEmail($user, $token);
    }

    /**
     * Inform the person that they have been granted access to a resource on the
     * site.
     */
    public function onAceCreatedEvent(AccessControlEntryEvent $event)
    {
        $ace = $event->getAccessControlEntry();
        switch ($ace->getType()) {
            case 'show':
            case 'society':
            case 'venue':
                $this->dispatcher->sendAceEmail($ace);
                break;
            case 'request-show':
                $this->dispatcher->sendShowAdminReqEmail($ace);
                break;
        }
    }

    /**
     * Inform the person that they have been granted access to a resource on the
     * site, pending creating an account.
     */
    public function onPendingAccessCreatedEvent(PendingAccessEvent $event)
    {
        $pending_ace = $event->getPendingAccess();
        $this->dispatcher->sendPendingAceEmail($pending_ace);
    }

    /**
     * If already authorized, notify that venues have changed. Otherwise
     * re-do the approval checks.
     */
    public function onVenuesChangedEvent(VenueChangeEvent $event): void
    {
        if ($event->show->getAuthorised()) {
            $this->moderationManager->notifyVenueChanged($event->show, $event->addedVenues, $event->removedVenues);
        } else {
            $this->moderationManager->autoApproveOrEmailModerators($event->show);
        }
    }
}
