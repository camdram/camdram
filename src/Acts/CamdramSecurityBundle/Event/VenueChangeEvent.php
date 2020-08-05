<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Contracts\EventDispatcher\Event;

class VenueChangeEvent extends Event
{
    /** @var Show */
    public $show;
    /** @var int[] */
    public $addedVenues;
    /** @var int[] */
    public $removedVenues;

    /**
     * @param int[] $addedVenues
     * @param int[] $removedVenues
     */
    public function __construct(Show $show, array $addedVenues, array $removedVenues)
    {
        $this->show = $show;
        $this->addedVenues = $addedVenues;
        $this->removedVenues = $removedVenues;
    }
}
