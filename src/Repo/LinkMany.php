<?php

namespace Harp\Harp\Repo;

use Harp\Harp\Rel\AbstractRelMany;
use Harp\Harp\Rel\DeleteManyInterface;
use Harp\Harp\Rel\InsertManyInterface;
use Harp\Harp\Rel\UpdateManyInterface;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Countable;
use Closure;
use Iterator;

/**
 * Represents a link between one model and many other "foreign" models.
 * It is the result of a "many" relation (RelMany).
 *
 * Tracks changes so you can retrieve original models, and know which ones were added / deleted.
 * Can also act as an iterater so you can foreach all the foreign models.
 * RepoModels is used to store the models internaly, so that adding foreign models from
 * different repos will result in an exception.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LinkMany extends AbstractLink implements Countable, Iterator
{
    /**
     * @var Models
     */
    private $original;

    /**
     * @var Models
     */
    private $current;

    /**
     * @param AbstractRelMany $rel
     * @param AbstractModel[] $models
     */
    public function __construct(AbstractModel $model, AbstractRelMany $rel, array $models)
    {
        $this->current = new RepoModels($rel->getRepo(), $models);
        $this->original = new RepoModels($rel->getRepo(), $models);

        parent::__construct($model, $rel);
    }

    /**
     * @return AbstractRelMany
     */
    public function getRel()
    {
        return parent::getRel();
    }

    /**
     * @return Models
     */
    public function get()
    {
        return $this->current;
    }

    /**
     * @return Models
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return boolean
     */
    public function isChanged()
    {
        return $this->current != $this->original;
    }

    /**
     * @return Models
     */
    public function getAdded()
    {
        $added = clone $this->current;
        $added->removeAll($this->original);

        return $added;
    }

    /**
     * @return Models
     */
    public function getRemoved()
    {
        $removed = clone $this->original;
        $removed->removeAll($this->current);

        return $removed;
    }

    /**
     * Used in the saving process.
     *
     * @return Models
     */
    public function getCurrentAndOriginal()
    {
        $all = clone $this->current;
        $all->addAll($this->original);

        return $all;
    }

    /**
     * If no first, will return void model
     *
     * @return AbstractModel
     */
    public function getFirst()
    {
        return $this->current->getFirst();
    }

    /**
     * Return next model, void model if no model
     *
     * @return AbstractModel
     */
    public function getNext()
    {
        return $this->current->getNext();
    }

    /**
     * Used by DeleteManyInterface relations, in the saving process
     *
     * @return Models|null
     */
    public function delete()
    {
        $rel = $this->getRel();
        if ($rel instanceof DeleteManyInterface) {
            return $rel->delete($this);
        }
    }

    /**
     * Used by InsertManyInterface relations, in the saving process
     *
     * @return Models|null
     */
    public function insert()
    {
        $rel = $this->getRel();

        if ($rel instanceof InsertManyInterface) {
            return $rel->insert($this);
        }
    }

    /**
     * Used by UpdateManyInterface relations, in the saving process
     *
     * @return Models|null
     */
    public function update()
    {
        $rel = $this->getRel();

        if ($rel instanceof UpdateManyInterface) {
            return $rel->update($this);
        }
    }

    /**
     * @param  AbstractModel[] $models
     * @return LinkMany        $this
     */
    public function addArray(array $models)
    {
        $this->current->addArray($models);

        return $this;
    }

    /**
     * @param  Models   $models
     * @return LinkMany $this
     */
    public function addModels(Models $models)
    {
        $this->current->addAll($models);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany      $this
     */
    public function add(AbstractModel $model)
    {
        $this->current->add($model);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany      $this
     */
    public function remove(AbstractModel $model)
    {
        $this->current->remove($model);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->current->isEmpty();
    }

    /**
     * @return LinkMany $this
     */
    public function clear()
    {
        $this->current->clear();

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->current->has($model);
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
        return $this->current->filter($filter);
    }

    /**
     * Call a method on each of the models, return the results as an array
     *
     * @param  string $methodName
     * @return array
     */
    public function invoke($methodName)
    {
        return $this->current->invoke($methodName);
    }

    /**
     * Call a closure for each model, return the results as an array
     *
     * @param  Closure $closure
     * @return array
     */
    public function map(Closure $closure)
    {
        return $this->current->map($closure);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->current->toArray();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->current->count();
    }

    /**
     * Implement Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->current->current();
    }

    /**
     * Implement Iterator
     */
    public function key()
    {
        return $this->current->key();
    }

    /**
     * Implement Iterator
     */
    public function next()
    {
        $this->current->next();

        return $this;
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        $this->current->rewind();

        return $this;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->current->valid();
    }
}
