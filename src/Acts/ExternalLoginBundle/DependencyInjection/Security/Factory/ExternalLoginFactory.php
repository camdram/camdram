<?php

namespace Acts\ExternalLoginBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\Parameter;
use Acts\CamdramSecurityBundle\Security\Service\ServiceProvider;

class ExternalLoginFactory extends AbstractFactory
{
    /**
     * Creates a auth service map for the given configuration.
     *
     * @param ContainerBuilder $container Container to build for
     * @param string           $id        Firewall id
     * @param array            $config    Configuration
     */
    protected function createServiceProvider(ContainerBuilder $container, $id, array $config)
    {
        $services = array();

        foreach ($config['services'] as $name) {
            $services[$name] = new Reference('external_login.service.'.$name);
        }
        //$container->setParameter('external_login.services.'.$id, $services);

        $container
            ->setDefinition($this->getServiceProviderReference($id), new DefinitionDecorator('external_login.service_provider'))
            ->replaceArgument(0, $services);
    }


    /**
     * Gets a reference to the resource service.
     *
     * @param string $id
     *
     * @return Reference
     */
    protected function getServiceProviderReference($id)
    {
        return new Reference('external_login.service_provider.'.$id);
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'external_login.authentication.provider.'.$id;

        $container
            ->setDefinition($provider, new DefinitionDecorator('external_login.authentication.provider'))
            ->replaceArgument(1, $this->getServiceProviderReference($id))
        ;

        return $provider;
    }

    protected function getListenerId()
    {
        return 'external_login.authentication.listener';
    }

    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $this->createServiceProvider($container, $id, $config);

        $container->getDefinition($listenerId)
            ->addMethodCall('setUrls', array(array('entry' => $config['entry_url'], 'auth' => $config['auth_url'])))
            ->addMethodCall('setServiceProvider', array($this->getServiceProviderReference($id)));


        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        /*$entryPointId = 'camdram.security.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('camdram.security.entry_point'))
            ->addArgument($config['login_path'])
        ;

        return $entryPointId;*/
        return $defaultEntryPoint;
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'external_login';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
            ->scalarNode('entry_url')->defaultValue('/extauth/redirect')->end()
            ->scalarNode('auth_url')->defaultValue('/extauth/login')->end()
            ->arrayNode('services')
                ->isRequired()
                ->prototype('scalar')
                ->end();
    }

}
