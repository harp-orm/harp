<?php

namespace Harp\Harp\Model;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait UnmappedPropertiesTrait
{
    private $unmapped;

    public function __get($name)
    {
        return isset($this->unmapped[$name]) ? $this->unmapped[$name] : null;
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
