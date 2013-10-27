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

    private function decompose($key)
    {
        $parts = explode('.',$key,2);
        if (count($parts) == 2) return $parts;
        else return false;
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
        $key = $this->convertKey($key);
        $composite = $this->decompose($key);
        if ($composite) {
            return $this->convertValue($this->data[$composite[0]][$composite[1]]);
        }
        else return $this->convertValue($this->data[$key]);
    }

    public function offsetSet($key, $value)
    {
        $key = $this->convertKey($key);
        $composite = $this->decompose($key);
        if ($composite) {
            $this->data[$composite[0]][$composite[1]] = $this->convertValue($value);
        }
        else $this->data[$key] = $value;
    }

    public function offsetExists($key)
    {
        $key = $this->convertKey($key);
        $composite = $this->decompose($key);
        if ($composite) {
            return isset($this->data[$composite[0]][$composite[1]]);
        }
        else return isset($this->data[$key]);
    }

    public function offsetUnset($key)
    {
        $key = $this->convertKey($key);
        $composite = $this->decompose($key);
        if ($composite) {
            unset($this->data[$composite[0]][$composite[1]]);
        }
        else unset($this->data[$key]);
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
        $current = current($this->data);
        return $this->convertValue($current);
    }

    public function key()
    {
        return $this->convertKey(key($this->data));
    }
}