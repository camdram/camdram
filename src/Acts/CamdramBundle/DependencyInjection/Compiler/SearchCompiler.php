<?php

namespace Acts\CamdramBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use InvalidArgumentException;

/**
 * Registers Transformer implementations into the TransformerCollection.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SearchCompiler implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('foq_elastica.client.class', 'Acts\CamdramBundle\Service\Search\ElasticaClient');
    }
}