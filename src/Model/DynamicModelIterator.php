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
class DynamicModelIterator extends ModelIterator
{
    private $original;

    public function executeLoader()
    {
        parent::executeLoader();

        $this->original = clone $this->models;
    }

    /**
     * Link all models from the SplObjectStorage
     *
     * @param  SplObjectStorage $models
     * @return Models           $this
     */
    public function addAll(ModelIterator $models)
    {
        $this->models->addAll($models->all());

        return $this;
    }

    /**
     * @param  array  $models
     * @return Models $this
     */
    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return Models        $this
     */
    public function add(AbstractModel $model)
    {
        $this->models->attach($model);

        return $this;
    }

    /**
     * @return Models $this
     */
    public function clear()
    {
        $this->models = new SplObjectStorage();

        return $this;
    }

    public function isChanged()
    {
        return $this->original != $this->models;
    }

    /**
     * @return Models
     */
    public function getAdded()
    {
        $added = clone $this->models;
        $added->removeAll($this->original);

        return $added;
    }

    /**
     * @return Models
     */
    public function getRemoved()
    {
        $removed = clone $this->original;
        $removed->removeAll($this->models);

        return $removed;
    }

    /**
     * @return Models
     */
    public function getCurrentAndOriginal()
    {
        $all = clone $this->original;
        $all->addAll($this->current);

        return $all;
    }

}
