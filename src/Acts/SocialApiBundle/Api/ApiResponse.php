<?php
namespace Acts\SocialApiBundle\Api;

class ApiResponse implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * The config detailing the response mapping
     *
     * @var array
     */
    private $config;

    /**
     * An array of the data returned from the api
     *
     * @var array
     */
    private $data;

    private function __construct(array &$data, array $config)
    {
        $this->config = $config;

        if (!is_null($config['root'])
                && isset($data[$config['root']])) {
            $this->data =& $data[$config['root']];
        }
        else {
            $this->data =& $data;
        }
    }

    public static function factory($data, $config = array('root' => null, 'map' => array()) )
    {
        return new ApiResponse($data, $config);
    }

    private function convertKey($key)
    {
        if (isset($this->config['map'][$key])) {
            return $this->config['map'][$key];
        }
        else return $key;
    }

    private function convertValue(&$val)
    {
        if (is_array($val)) {
            return new ApiResponse($val, $this->config);
        }
        else {
            return $val;
        }
    }

    public function offsetGet($key)
    {
        return $this->convertValue($this->data[$this->convertKey($key)]);
    }

    public function offsetSet($key, $value)
    {
        $this->data[$this->convertKey($key)] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->data[$this->convertKey($key)]);
    }

    public function offsetUnset($key)
    {
        unset($this->data[$this->convertKey($key)]);
    }

    public function count()
    {
        return count($this->data);
    }

    public function valid()
    {
        return current($this->data) !== false;
    }

    public function rewind()
    {
        return reset($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function current()
    {
        return $this->convertValue(current($this->data));
    }

    public function key()
    {
        return $this->convertKey(key($this->data));
    }
}