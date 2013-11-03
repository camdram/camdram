<?php
namespace Acts\SocialApiBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;

use Acts\SocialApiBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testMinimal()
    {
        $config = array('apis' => array());

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, array($config));
    }

    public function testNoConfigForApi()
    {
        $config = array(
            'apis' => array(
                'facebook' => array()
            )
        );

        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, array($config));
    }

    public function testExtraConfigForApi()
    {
        $config = array(
            'apis' => array(
                'facebook' => array(
                    'client_id' => 'iiiiiii',
                    'client_secret' => 'ssssss',
                    'base_url' => 'https://graph.facebook.com',
                    'login_url' => 'https://www.facebook.com/dialog/oauth',
                    'extra_config' => 'this value should not be here',
                )
            )
        );

        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, array($config));
    }

    public function testDefaults()
    {
        $config = array(
            'apis' => array(
                'facebook' => array(
                    'client_id' => 'iiiiiii',
                    'client_secret' => 'ssssss',
                    'base_url' => 'https://graph.facebook.com',
                    'login_url' => 'https://www.facebook.com/dialog/oauth',
                )
            )
        );

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array($config));

        $this->assertEquals(array(
            'apis' => array(
                'facebook' => array(
                    'client_id' => 'iiiiiii',
                    'client_secret' => 'ssssss',
                    'base_url' => 'https://graph.facebook.com',
                    'login_url' => 'https://www.facebook.com/dialog/oauth',
                    'class' => 'rest',
                    'paths' => array()
                )
            ),
            'http_client' => array(
                'timeout' => 10,
                 'verify_peer' => true,
                 'max_redirects' => 5,
                'ignore_errors' => true,
                'user_agent' => ''
            )
        ), $config);
    }

    public function testDefaultPathOptions()
    {
        $config = array(
            'apis' => array(
                'facebook' => array(
                    'client_id' => 'iiiiiii',
                    'client_secret' => 'ssssss',
                    'base_url' => 'https://graph.facebook.com',
                    'login_url' => 'https://www.facebook.com/dialog/oauth',
                    'paths' => array('search' => array('path' => '/search'))
                )
            )
        );

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array($config));

        $this->assertEquals(array(
            'search' => array(
                'path' => '/search',
                'method' => 'GET',
                'requires_authentication' => true,
                'arguments' => array(),
                'response' => array(
                    'root' => null,
                    'map' => array(),
                ),
                'url_has_params' => false,
                'defaults' => array()
            )
        ), $config['apis']['facebook']['paths']);
    }

}