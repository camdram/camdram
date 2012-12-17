<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Acts\SocialApiBundle\Utils\Inflector;
use Symfony\Component\DependencyInjection\Container;

class InflectorrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Acts\SocialApiBundle\Utils\Inflector;
     */
    private $inflector;

    public function setUp()
    {
        $this->inflector = new Inflector;
    }

    public function testCamelCaseToUnderscore()
    {
        $this->assertEquals('', $this->inflector->underscore(''));
        $this->assertEquals('one', $this->inflector->underscore('one'));
        $this->assertEquals('one_two', $this->inflector->underscore('oneTwo'));
        $this->assertEquals('one_two_three', $this->inflector->underscore('oneTwoThree'));
    }

    public function testUnderscoreToCamelCase()
    {
        $this->assertEquals('', $this->inflector->camelize(''));
        $this->assertEquals('One', $this->inflector->camelize('one'));
        $this->assertEquals('OneTwo', $this->inflector->camelize('one_two'));
        $this->assertEquals('OneTwoThree', $this->inflector->camelize('oneTwoThree'));
    }
}
