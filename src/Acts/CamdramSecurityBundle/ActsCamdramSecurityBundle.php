<?php

namespace Acts\CamdramSecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Acts\CamdramSecurityBundle\DependencyInjection\Security\Factory\CamdramFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Acts\CamdramSecurityBundle\DependencyInjection\Compiler\SetupAclVoterPass;

class ActsCamdramSecurityBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SetupAclVoterPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CamdramFactory());
    }
}
