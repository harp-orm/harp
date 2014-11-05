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
class StaticLoader
{
    private $models;

    /**
     * @param AbstractModel[]|null $models
     */
    public function __construct(array $models)
    {
        $this->models = $models;
    }

    public function load()
    {
        $result = new SplObjectStorage();

        foreach ($this->models as $model) {
            $result->attach($model);
        }

        return $result;
    }
}
