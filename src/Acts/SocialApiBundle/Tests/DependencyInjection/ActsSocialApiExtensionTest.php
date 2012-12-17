<?php
namespace Acts\SocialApiBundle\Tests\DependencyInjection;

use Acts\SocialApiBundle\DependencyInjection\ActsSocialApiExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ActsSocialApiTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder;
     */
    private $container;

    private $config1 = array(
        'apis' => array(
            'facebook' => array(
                'client_id' => 'iiiiiii',
                'client_secret' => 'ssssss',
                'base_url' => 'https://graph.facebook.com',
                'login_url' => 'https://www.facebook.com/dialog/oauth',
            )
        )
    );

    private $config2 = array(
        'apis' => array(
            'facebook' => array(
                'client_id' => 'iiiiiii',
                'client_secret' => 'ssssss',
                'base_url' => 'https://graph.facebook.com',
                'login_url' => 'https://www.facebook.com/dialog/oauth',
            ),
            'twitter' => array(
                'client_id' => 'iiiiiii',
                'client_secret' => 'ssssss',
            )
        )
    );

    public function setUp()
    {
        $this->container = new ContainerBuilder();
    }

    public function testProcessDefaultConfiguration()
    {
        $extension = new ActsSocialApiExtension();
        $config = array($this->config1);
        $extension->processDefaultConfiguration($config);

        $this->assertArrayHasKey('paths', $config[0]['apis']['facebook']);
        $this->assertArrayHasKey('class', $config[0]['apis']['facebook']);
        $this->assertArrayHasKey('login_url', $config[0]['apis']['facebook']);
    }

    public function testServices()
    {
        $extension = new ActsSocialApiExtension();
        $extension->load(array($this->config2), $this->container);

        $def = $this->container->getDefinition('acts.social_api.apis.facebook');
        $this->assertEquals('acts.social_api.apis.abstract.oauth2', $def->getParent());
        $this->assertEquals('facebook', $def->getArgument(0));
        $this->assertArrayHasKey('client_id', $def->getArgument(1));

        $def = $this->container->getDefinition('acts.social_api.apis.twitter');
        $this->assertEquals('acts.social_api.apis.abstract.oauth1', $def->getParent());
        $this->assertEquals('twitter', $def->getArgument(0));
        $this->assertArrayHasKey('client_id', $def->getArgument(1));

        $def = $this->container->getDefinition('acts.social_api.provider');
        $this->assertEquals(array('facebook', 'twitter'), $def->getArgument(1));
    }
}
