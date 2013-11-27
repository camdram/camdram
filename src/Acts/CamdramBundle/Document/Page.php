<?php

namespace Acts\CamdramBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;

/**
 * @PHPCR\Document(referenceable=true)
 */
class Page implements RouteReferrersReadInterface
{
    use ContentTrait;
}

