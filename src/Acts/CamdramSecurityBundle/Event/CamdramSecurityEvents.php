<?php
namespace Acts\CamdramSecurityBundle\Event;

class CamdramSecurityEvents
{
    const EMAIL_VERIFIED         = 'camdram_security.email_verified';
    const EMAIL_CHANGED          = 'camdram_security.email_changed';
    const PASSWORD_CHANGED       = 'camdram_security.password_changed';
    const PENDING_ACCESS_CREATED = 'camdram_security.pending_access_created';
    const REGISTRATION_COMPLETE  = 'camdram_security.registration_complete';
}

