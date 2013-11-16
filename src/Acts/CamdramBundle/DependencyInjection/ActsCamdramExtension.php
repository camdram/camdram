<?php

namespace Acts\CamdramBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ActsCamdramExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setDefinition('acts.camdram.search_provider',
            new DefinitionDecorator('acts.camdram.search_provider.'.$config['search_provider'])
        );

        $container->getDefinition('acts.camdram.listener.vacancies')->addArgument($config['techies_advert_default_days']);
        $container->getDefinition('acts.camdram.techie_advert_expiry_validator')->addArgument($config['techies_advert_max_days']);
    }
}
