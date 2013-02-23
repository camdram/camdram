<?php

namespace Acts\CamdramSecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Acts\CamdramSecurityBundle\DependencyInjection\Security\Factory\CamdramFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ActsCamdramSecurityBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CamdramFactory());
    }
}
