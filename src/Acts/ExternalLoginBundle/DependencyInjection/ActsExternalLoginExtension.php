<?php

namespace Acts\ExternalLoginBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ActsExternalLoginExtension extends Extension
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

        if ($config['test'] && $container->getParameter('kernel.environment') == 'prod') {
            throw new InvalidConfigurationException("The external login bundle's 'test' mode cannot be turned on in production");
        }

        foreach ($config['services'] as $name => $options) {
            if ($config['test']) {
                $name = $this->createTestAuthService($container, $name, $options);
            }
            else {
                $name = $this->createAuthService($container, $name, $options);
            }
            $authServices[] = new Reference($name);
        }

        $container->getDefinition('external_login.service_provider')->addArgument($authServices);
        $container->getDefinition('external_login.authentication.provider')
            ->replaceArgument(0, new Reference($config['user_provider_id']));
        $container->setParameter('external_login.default_firewall', $config['default_firewall']);
    }

    public function createAuthService(ContainerBuilder $container, $name, array $options)
    {
        $service_name = 'external_login.service.'.$name;

        if ($options['class'] == $name) {
            $definition = $container->getDefinition($service_name);
        }
        else {
            $definition = $container->setDefinition($service_name,
                new DefinitionDecorator('external_login.service.'.$options['class']));
        }

        $definition->addArgument($name)
            ->addArgument($options['settings']);

        if ($options['class'] == 'social_api') {
            $definition->addMethodCall('setApi', array(new Reference('acts.social_api.apis.'.$options['settings']['api'])));
        }
        return $service_name;
    }

    public function createTestAuthService(ContainerBuilder $container, $name, array $options)
    {
        $service_name = 'external_login.service.'.$name;
        $definition = $container->setDefinition($service_name, new DefinitionDecorator('external_login.service.test'));
        $definition->addArgument($name);
        return $service_name;
    }
}
