<?php
namespace Acts\SocialApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ActsSocialApiExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processDefaultConfiguration($configs);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $httpClient = $container->getDefinition('buzz.client');
        $httpClient->addMethodCall('setVerifyPeer', array($config['http_client']['verify_peer']));
        $httpClient->addMethodCall('setTimeout', array($config['http_client']['timeout']));
        $httpClient->addMethodCall('setMaxRedirects', array($config['http_client']['max_redirects']));
        $httpClient->addMethodCall('setIgnoreErrors', array($config['http_client']['ignore_errors']));
        $container->setDefinition('acts.social_api.http_client', $httpClient);

        $api_names = array();

        $authServices = array();
        foreach ($config['apis'] as $name => $options) {
            $authServices[] = $name;
            $this->createApi($container, $name, $options);
            $api_names[] = $name;
        }

        $container->getDefinition('acts.social_api.provider')
            ->addArgument($api_names);

    }

    public function processDefaultConfiguration(array &$configs)
    {
        $defaults = Yaml::parse(__DIR__.'/../Resources/config/defaults.yml');
        if (isset($configs[0]['apis'])) {
            foreach ($configs[0]['apis'] as $name => &$api) {
                if  (isset($defaults[$name])) {
                    $api = array_replace_recursive($api, $defaults[$name]);
                }
            }
        }
    }

    public function createApi(ContainerBuilder $container, $name, array $options)
    {
        $class = $options['class'];
        $container
            ->setDefinition(new Reference('acts.social_api.apis.'.$name), new DefinitionDecorator('acts.social_api.apis.abstract.'.$class))
            ->addArgument($name)
            ->addArgument($options);

    }
}
