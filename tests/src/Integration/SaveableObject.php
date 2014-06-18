<?php

namespace Harp\Harp\Test\Integration;

use Serializable;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SaveableObject implements Serializable {

    private $var;

    public function getVar()
    {
        return $this->var;
    }

    public function setVar($var)
    {
        $this->var = $var;

        return $this;
    }

    public function serialize()
    {
        return serialize([$this->var]);
    }

    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->var = $data[0];
    }
}
