<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Acts\SocialApiBundle\Api\ApiResponse;

class ApiResponseTest extends \PHPUnit_Framework_TestCase
{
    private $config = array(
        'root' => '',
        'map' => array()
    );

    private $data = array(
        'code' => 2,
        array(
            'id' => 0,
            'name' => 'foo',
            'type' => 'x',
        ),
        array(
            'id' => 1,
            'name' => 'bar',
            'type' => 'y',
        ),
    );

    /**
     * @param $data
     * @param $config
     * @return \Acts\SocialApiBundle\Api\ApiResponse
     */
    public function getResponse($data, $config)
    {
        return ApiResponse::factory($data, $config);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('\Acts\SocialApiBundle\Api\ApiResponse', $this->getResponse($this->data, $this->config));
    }

    public function testGet()
    {
        $resp = $this->getResponse($this->data, $this->config);
        $this->assertEquals(2, $resp['code']);
    }

    public function testCount()
    {
        $resp = $this->getResponse($this->data, $this->config);
        $this->assertEquals(3, count($resp));
    }

    public function testExists()
    {
        $resp = $this->getResponse($this->data, $this->config);
        $this->assertTrue(isset($resp['code']));
        $this->assertFalse(isset($resp['invalid_key']));
    }

    public function testSet()
    {
        $resp = $this->getResponse($this->data, $this->config);
        $resp[0]['extra_data'] = 'val';
        $this->assertEquals('val', $resp[0]['extra_data']);
    }

    public function testUnset()
    {
        $resp = $this->getResponse($this->data, $this->config);
        unset($resp['code']);
        $this->assertFalse(isset($resp['code']));
    }

    public function testNestedGet()
    {
        $resp = $this->getResponse($this->data, $this->config);
        $this->assertEquals('bar', $resp[1]['name']);
    }

    public function testRoot()
    {
        $data = array('data' => array('foo' => 'bar'));
        $config = array('root' => 'data', 'map' => array());
        $resp = $this->getResponse($data, $config);
        $this->assertEquals('bar', $resp['foo']);
    }

    public function testMap()
    {
        $data = array('foo2' => 'bar');
        $config = array('root' => null, 'map' => array('foo' => 'foo2'));
        $resp = $this->getResponse($data, $config);
        $this->assertEquals('bar', $resp['foo']);
    }

    public function testNestedMap()
    {
        $config = array('root' => null, 'map' => array('full_name' => 'name'));
        $resp = $this->getResponse($this->data, $config);
        $this->assertEquals('foo', $resp[0]['full_name']);
    }

    public function testIterate()
    {
        $resp = $this->getResponse($this->data, $this->config);
        foreach ($resp as $key => $item) {
            $this->assertEquals($item, $resp[$key]);
        }
    }
}