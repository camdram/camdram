<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\TimeMockBundle\Service\TimeService;

/**
 * Class VacanciesListener
 *
 * Listens for new/modified vacancies
 *
 * @package Acts\CamdramBundle\EventListener
 */
class TechieAdvertListener
{
    private $timeService;

    public function __construct(TimeService $timeService, $default_expiry_days)
    {
        $this->timeService = $timeService;
        $this->default_expiry_days = $default_expiry_days;
    }

    public function prePersist(TechieAdvert $techieAdvert)
    {
        $this->updateExpiryDate($techieAdvert);
    }

    public function preUpdate(TechieAdvert $techieAdvert)
    {
        $techieAdvert->setUpdatedAt($this->timeService->getCurrentTime());
        $this->updateExpiryDate($techieAdvert);
    }

    private function updateExpiryDate(TechieAdvert $techieAdvert)
    {
        if (!$techieAdvert->getDeadline()) {
            $expires = $this->timeService->getCurrentTime();
            $expires->modify('+'.$this->default_expiry_days.' days');
            $techieAdvert->setDeadlineTime(new \DateTime('00:00'));
            $techieAdvert->setExpiry($expires);
        }
    }

}