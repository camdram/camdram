<?php

namespace Acts\CamdramBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Acts\CamdramBundle\DependencyInjection\Compiler\DoctrineEntityListenerPass;

class ActsCamdramBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineEntityListenerPass());
    }
}
