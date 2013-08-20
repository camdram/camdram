<?php

namespace Acts\ExternalLoginBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('acts_external_login');

        $rootNode
            ->isRequired()
            ->cannotBeEmpty()
            ->children()
                ->scalarNode('default_firewall')->defaultValue('public')->end()
                ->arrayNode('groups')
                ->prototype('array')
                ->treatNullLike(array())
                    ->children()
                        ->arrayNode('roles')->prototype('scalar')->treatNullLike(array())->end()->end()
                    ->end()->end()
                ->end()
                ->arrayNode('services')
                    ->isRequired()
                    ->prototype('array')
                    ->children()
                        ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('settings')->prototype('scalar')->end()
                    ->end()
            ->end()->end();

        return $treeBuilder;
    }
}
