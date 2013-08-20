<?php

namespace Acts\ExternalLoginBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Acts\ExternalLoginBundle\DependencyInjection\Security\Factory\ExternalLoginFactory;

class ActsExternalLoginBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ExternalLoginFactory());
    }
}
