<?php

namespace Harp\Harp\Model;

use Harp\Harp\AbstractModel;
use SplObjectStorage;
use Countable;
use Iterator;

/**
 * A collection of model unique objects.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SelectLoader
{
    private $select;

    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    public function load()
    {
        $result = new SplObjectStorage();

        foreach ($this->select->execute() as $model) {
            $result->attach($model);
        }

        return $result;
    }
}
