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
                ->children()
                    ->arrayNode('facebook')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('client_id')->cannotBeEmpty()->isRequired()->end()
                            ->scalarNode('client_secret')->cannotBeEmpty()->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('twitter')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('google')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('windows_live')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('yahoo')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('raven')
                        ->treatNullLike(array())
                        ->children()
                            ->scalarNode('description')->info('website description given to Raven')->example('"My App"')->end()
                        ->end()
                     ->end()
                    ->arrayNode('local')
                    ->treatNullLike(array())
                        ->children()
                        ->end()
                    ->end()
                ->end()
            ->end()->end();


        return $treeBuilder;
    }
}
