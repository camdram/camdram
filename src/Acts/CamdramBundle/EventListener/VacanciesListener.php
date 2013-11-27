<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Event\TechieAdvertEvent;
use Acts\TimeMockBundle\Service\TimeService;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class VacanciesListener
 *
 * Listens for new/modified vacancies
 *
 * @package Acts\CamdramBundle\EventListener
 */
class VacanciesListener
{
    private $timeService;

    public function __construct(TimeService $timeService, $default_expiry_days)
    {
        $this->timeService = $timeService;
        $this->default_expiry_days = $default_expiry_days;
    }

    public function onTechieAdvertCreated(TechieAdvertEvent $event)
    {
        $this->updateExpiryDate($event->getTechieAdvert());
    }

    public function onTechieAdvertEdited(TechieAdvertEvent $event)
    {
        $this->updateExpiryDate($event->getTechieAdvert());
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