<?php

namespace Harp\Harp\Test\Integration;

use Serializable;

/**
 * @group integration
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
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
