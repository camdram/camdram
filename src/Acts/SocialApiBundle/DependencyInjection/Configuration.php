<?php
namespace Acts\SocialApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

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
        $rootNode = $treeBuilder->root('acts_social_api');

        $data =

        $apiNode = $rootNode
            ->isRequired()
            ->children()
                ->arrayNode('http_client')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('timeout')->defaultValue(10)->cannotBeEmpty()->end()
                        ->booleanNode('verify_peer')->defaultTrue()->end()
                        ->scalarNode('max_redirects')->defaultValue(5)->cannotBeEmpty()->end()
                        ->booleanNode('ignore_errors')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('apis')
                ->isRequired()
                ->useAttributeAsKey('')
                ->prototype('array')
                ->treatNullLike(array())
                ->children()
                    ->scalarNode('client_id')->end()
                    ->scalarNode('client_secret')->end()
                    ->scalarNode('key')->end()
                    ->scalarNode('access_token')->end()
                    ->scalarNode('access_token_secret')->end()
                    ->scalarNode('base_url')->isRequired()->end()
                    ->scalarNode('class')->defaultValue('rest')->end()
                    ->scalarNode('login_url')->end()
                    ->scalarNode('scope')->end()
                    ->arrayNode('paths')
                    ->prototype('array')
                    ->isRequired()
                    ->children()
                        ->scalarNode('path')->isRequired()->end()
                        ->scalarNode('url_has_params')->defaultFalse()->end()
                        ->arrayNode('defaults')->prototype('scalar')->treatNullLike(array())->end()->end()
                        ->scalarNode('method')->defaultValue('GET')->end()
                        ->scalarNode('requires_authentication')->defaultValue(true)->end()
                        ->arrayNode('arguments')->prototype('scalar')->treatNullLike(array())->end()->end()
                        ->arrayNode('response')->addDefaultsIfNotSet()->treatNullLike(array())
                            ->children()
                                ->scalarNode('root')->defaultValue(null)->end()
                                ->arrayNode('map')->prototype('scalar')->treatNullLike(array())->end()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;


        $apiNode = $apiNode->end()->end();
        return $treeBuilder;
    }

}
