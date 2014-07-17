<?php

namespace Harp\Harp\Model;

use Harp\Util\Objects;
use Harp\Harp\AbstractModel;
use SplObjectStorage;
use Closure;
use Countable;
use Iterator;
use LogicException;

/**
 * A collection of model unique objects.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Models implements Countable, Iterator
{
    /**
     * @var SplObjectStorage
     */
    private $models;

    public function __construct(array $models = null)
    {
        $this->models = new SplObjectStorage();

        if ($models) {
            $this->addArray($models);
        }
    }

    /**
     * Clone internal SplObjectStorage
     */
    public function __clone()
    {
        $this->models = clone $this->models;
    }

    /**
     * Link all models from the SplObjectStorage
     *
     * @param  SplObjectStorage $models
     * @return Models           $this
     */
    public function addObjects(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * Add all models from a different Models collection
     *
     * @param Models $other
     */
    public function addAll(Models $other)
    {
        if ($other->count() > 0) {
            $this->models->addAll($other->models);
        }

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

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->models->contains($model);
    }

    /**
     * @param  string|integer $id
     * @return boolean
     */
    public function hasId($id)
    {
        return array_search($id, $this->getIds()) !== false;
    }

    /**
     * @return AbstractModel|null
     */
    public function getFirst()
    {
        $this->models->rewind();

        return $this->models->current();
    }

    /**
     * @return AbstractModel|null
     */
    public function getNext()
    {
        $this->models->next();

        return $this->models->current();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->models->count();
    }

    /**
     * Return containing models
     *
     * @return SplObjectStorage
     */
    public function all()
    {
        return $this->models;
    }

    /**
     * Call "validate" method on all the models, throw a LogicException if any of them has validation errors.
     *
     * @throws LogicException If a model is invalid
     * @return Models         $this
     */
    public function assertValid()
    {
        foreach ($this->models as $model) {
            if (! $model->validate()) {
                throw new LogicException(
                    sprintf('%s contains errors: %s', $model->getRepo()->getName(), $model->getErrors()->humanize())
                );
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return Objects::toArray($this->models);
    }

    /**
     * @param  AbstractModel $model
     * @return Models        $this
     */
    public function remove(AbstractModel $model)
    {
        unset($this->models[$model]);

        return $this;
    }

    /**
     * @param  Models $models
     * @return Models $this
     */
    public function removeAll(Models $models)
    {
        $this->models->removeAll($models->all());

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->models) === 0;
    }

    /**
     * Return a new Models object with only the models that pass the filter callback
     * (Filter callback returned true).
     *
     * @param  Closure $filter must return true for each item
     * @return Models  Filtered models
     */
    public function filter(Closure $filter)
    {
        $filtered = clone $this;

        $filtered->models = Objects::filter($filtered->models, $filter);

        return $filtered;
    }

    /**
     * Sort the models collection using a comparation closure
     *
     * @param  Closure $closure
     * @return array
     */
    public function sort(Closure $closure)
    {
        $sorted = clone $this;

        $sorted->models = Objects::sort($sorted->models, $closure);

        return $sorted;
    }

    /**
     * Call a method on each of the models, return the results as an array
     *
     * @param  string $methodName
     * @return array
     */
    public function invoke($methodName)
    {
        return Objects::invoke($this->models, $methodName);
    }

    /**
     * Call a closure for each model, return the results as an array
     *
     * @param  Closure $closure
     * @return array
     */
    public function map(Closure $closure)
    {
        return Objects::map($this->models, $closure);
    }

    /**
     * Group models by repo, call yield for each repo
     *
     * @param Closure $yield Call for each repo ($repo, $models)
     */
    public function byRepo(Closure $yield)
    {
        $repos = Objects::groupBy($this->models, function (AbstractModel $model) {
            return $model->getRepo()->getRootRepo();
        });

        foreach ($repos as $repo) {
            $models = new Models();
            $models->addObjects($repos->getInfo());

            $yield($repo, $models);
        }
    }

    /**
     * Return the value of a property for each model
     *
     * @param  string $property
     * @return array
     */
    public function pluckProperty($property)
    {
        $values = [];

        foreach ($this->models as $model) {
            $values []= $model->$property;
        }

        return $values;
    }

    /**
     * Return false if there is at least one non-empty property of a model.
     *
     * @param  string  $property
     * @return boolean
     */
    public function isEmptyProperty($property)
    {
        foreach ($this->models as $model) {
            if ($model->$property) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return only unique values from pluckProperty
     *
     * @param  string $property
     * @return array
     */
    public function pluckPropertyUnique($property)
    {
        return array_unique(array_filter($this->pluckProperty($property)));
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return Objects::invoke($this->models, 'getId');
    }

    /**
     * Implement Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->models->current();
    }

    /**
     * Implement Iterator
     */
    public function key()
    {
        return $this->models->key();
    }

    /**
     * Implement Iterator
     *
     * @return Models
     */
    public function next()
    {
        $this->models->next();

        return $this;
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        $this->models->rewind();

        return $this;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->models->valid();
    }
}
