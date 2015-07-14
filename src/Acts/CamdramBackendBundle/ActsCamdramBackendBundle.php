<?php

namespace Acts\CamdramBackendBundle;

use Acts\CamdramBackendBundle\DependencyInjection\Compiler\DoctrineEntityListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ActsCamdramBackendBundle
 *
 * The ActsCamdramBackendBundle contains administrative stuff to keep it separate from the core, user-facing code in
 * CamdramBundle e,g, console commands,
 */
class ActsCamdramBackendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineEntityListenerPass());
    }
}
