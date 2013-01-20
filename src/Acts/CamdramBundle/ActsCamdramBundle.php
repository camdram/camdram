<?php

namespace Acts\CamdramBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Acts\CamdramBundle\DependencyInjection\Compiler\SearchCompiler;

class ActsCamdramBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SearchCompiler());
    }
}
