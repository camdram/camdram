<?php

namespace Acts\CamdramSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Acts\CamdramSecurityBundle\DependencyInjection\Security\Factory\CamdramFactory;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ActsCamdramSecurityExtension extends Extension
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

        $authServices = array();
        foreach ($config['services'] as $name => $options) {
            $authServices[] = $name;
            $this->createAuthService($container, $name, $options);
        }
        $container->setParameter('camdram.security.services', $authServices);
        $container->setParameter('camdram.security.default_firewall', $config['default_firewall']);

    }

    public function createAuthService(ContainerBuilder $container, $name, array $options)
    {
        $definition = $container->getDefinition('camdram.security.service.'.$name);

        $definition->addArgument( $name)
            ->addArgument($options);
    }

}
