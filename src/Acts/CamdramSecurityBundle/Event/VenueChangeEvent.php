<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Contracts\EventDispatcher\Event;

class VenueChangeEvent extends Event
{
    public $show;
    public $addedVenues;
    public $removedVenues;

    public function __construct(Show $show, array $addedVenues, array $removedVenues)
    {
        $this->show = $show;
        $this->addedVenues = $addedVenues;
        $this->removedVenues = $removedVenues;
    }
}
