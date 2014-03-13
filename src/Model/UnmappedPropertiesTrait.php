<?php namespace CL\Luna\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait UnmappedPropertiesTrait
{
    private $unmapped;

    public function __get($name)
    {
        return isset($this->unmapped[$name]) ? $name : NULL;
    }

    public function __set($name, $value)
    {
        $this->unmapped[$name] = $value;
        return $this;
    }

    public function __isset($name)
    {
        return isset($this->unmapped[$name]);
    }

    public function getUnmapped()
    {
        return $this->unmapped;
    }
}
