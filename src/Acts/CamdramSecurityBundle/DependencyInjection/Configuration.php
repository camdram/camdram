<?php

namespace Acts\CamdramSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('acts_camdram_security');

        $rootNode
            ->isRequired()
            ->cannotBeEmpty()
            ->children()
            ->scalarNode('default_firewall')->defaultValue('public')->end()
            ->arrayNode('services')
                ->isRequired()
                ->prototype('array')
                ->children()
                    ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('id')->cannotBeEmpty()->end()
                    ->scalarNode('client_id')->cannotBeEmpty()->end()
                    ->scalarNode('client_secret')->cannotBeEmpty()->end()
                    ->scalarNode('description')->end()
                ->end()
            ->end()->end();


        return $treeBuilder;
    }
}
