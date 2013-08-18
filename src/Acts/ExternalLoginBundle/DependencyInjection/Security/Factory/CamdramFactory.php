<?php

namespace Acts\CamdramSecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\Parameter;
use Acts\CamdramSecurityBundle\Security\ServiceMap;

class CamdramFactory extends AbstractFactory
{
    /**
     * Creates a auth service map for the given configuration.
     *
     * @param ContainerBuilder $container Container to build for
     * @param string           $id        Firewall id
     * @param array            $config    Configuration
     */
    protected function createServiceMap(ContainerBuilder $container, $id, array $config)
    {
        $servicesMap = array();

        foreach ($config['services'] as $name => $options) {
            if (!isset($options['login_url'])) $options['login_url'] = '/auth/'.$name;
            if (!isset($options['connect_url'])) $options['connect_url'] = '/redirect/'.$name;
            if (!isset($options['display_name'])) $options['display_name'] = ucfirst($name);
            $servicesMap[$name] = $options;
        }
        $container->setParameter('camdram.security.service_map.configuration.'.$id, $servicesMap);

        $container
            ->setDefinition($this->getServiceMapReference($id), new DefinitionDecorator('camdram.security.service_map'))
            ->replaceArgument(3, new Parameter('camdram.security.service_map.configuration.'.$id));
    }


    /**
     * Gets a reference to the resource service.
     *
     * @param string $id
     *
     * @return Reference
     */
    protected function getServiceMapReference($id)
    {
        return new Reference('camdram.security.service_map.'.$id);
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'security.authentication.provider.camdram.'.$id;

        $this->createServiceMap($container, $id, $config);

        $newIdentityHandlerId = 'camdram.security.new_identity_handler';
        $container->setDefinition(new Reference($newIdentityHandlerId.'.'.$id), new DefinitionDecorator($newIdentityHandlerId))
            ->replaceArgument(2, $this->getServiceMapReference($id));


        $container
            ->setDefinition($provider, new DefinitionDecorator('camdram.security.authentication.provider'))
            ->replaceArgument(1, $this->getServiceMapReference($id))
        ;

        return $provider;
    }

    protected function getListenerId()
    {
        return 'camdram.security.authentication.listener';
    }

    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $service_config = $container->getParameter('camdram.security.service_map.configuration.'.$id);

        $checkPaths = array();
        foreach ($service_config as $name => $options) {
            $checkPaths[] = $options['login_url'];
        }



        $container->getDefinition($listenerId)
            ->addMethodCall('setServiceMap', array($this->getServiceMapReference($id)))
            ->addMethodCall('setCheckPaths', array($checkPaths))
            ->addMethodCall('setNewIdentityHandler', array(new Reference('camdram.security.new_identity_handler.'.$id)));


        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'camdram.security.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('camdram.security.entry_point'))
            ->addArgument($config['login_path'])
        ;

        return $entryPointId;
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'camdram';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
            ->scalarNode('login_path')->defaultValue('/login')->end()
            ->arrayNode('services')
                ->isRequired()
                ->prototype('array')
                ->children()
                    ->scalarNode('login_url')->end()
                    ->scalarNode('connect_url')->end()
                    ->scalarNode('display_name')->end()
                ->end();
    }

}
