<?php

namespace Harp\Harp;

use SplObjectStorage;
use Harp\Query\Select;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SelectLoader implements LoaderInterface
{
    private $select;

    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    public function getModels()
    {
        $array = $this->select->execute();
        $models = new SplObjectStorage();

        foreach ($array as $item) {
            $item = $this->select->getSession()->add($item);
            $models->attach($item);
        }

        return $models;
    }

    public function getSelect()
    {
        return $this->select;
    }

    public function getVoidModel()
    {
        return $this->select->getConfig()->newVoidModel();
    }
}
