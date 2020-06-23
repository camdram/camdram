<?php

namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\TechieAdvert;

/**
 * Class VacanciesListener
 *
 * Listens for new/modified vacancies
 */
class TechieAdvertListener
{
    private $default_expiry_days;

    public function __construct($default_expiry_days)
    {
        $this->default_expiry_days = $default_expiry_days;
    }

    public function prePersist(TechieAdvert $techieAdvert)
    {
        $this->updateExpiryDate($techieAdvert);
    }

    public function preUpdate(TechieAdvert $techieAdvert)
    {
        $techieAdvert->setUpdatedAt(new \DateTime);
        $this->updateExpiryDate($techieAdvert);
    }

    private function updateExpiryDate(TechieAdvert $techieAdvert)
    {
        if (!$techieAdvert->getDeadline()) {
            $expires = new \DateTime('midnight +'.$this->default_expiry_days.' days');
            $techieAdvert->setExpiry($expires);
        }
    }
}
