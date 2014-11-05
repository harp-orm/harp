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
class ModelIterator implements Countable, Iterator
{
    /**
     * @var SplObjectStorage
     */
    private $models;

    private $loader;

    private $loaded = false;

    /**
     * @param AbstractModel[]|null $models
     */
    public function __construct(ModelLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function isLoaded()
    {
        return $this->loaded;
    }

    public function getLoader()
    {
        return $loader;
    }

    /**
     * Clone internal SplObjectStorage
     */
    public function __clone()
    {
        $this->models = clone $this->models;
        $this->loader = clone $this->loader;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->all()->contains($model);
    }

    /**
     * @param  string|integer $id
     * @return boolean
     */
    public function hasId($id)
    {
        return false !== array_search($id, $this->getIds());
    }

    /**
     * @return AbstractModel|null
     */
    public function getFirst()
    {
        $this->all()->rewind();

        return $this->models->current();
    }

    /**
     * @return AbstractModel|null
     */
    public function getNext()
    {
        $this->all()->next();

        return $this->all()->current();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->all()->count();
    }

    /**
     * Return containing models
     *
     * @return SplObjectStorage
     */
    public function all()
    {
        if (false === $this->loaded) {
            $this->executeLoader();
            $this->loaded = true;
        }

        return $this->models;
    }

    public function executeLoader()
    {
        $this->models = $this->loader->load();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->all());
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->all()) === 0;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return array_map(function (AbstractModel $model) {
            return $model->getId();
        }, $this->toArray());
    }

    /**
     * Implement Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->all()->current();
    }

    /**
     * Implement Iterator
     */
    public function key()
    {
        return $this->all()->key();
    }

    /**
     * Implement Iterator
     *
     * @return Models
     */
    public function next()
    {
        $this->all()->next();

        return $this;
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        $this->all()->rewind();

        return $this;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->all()->valid();
    }
}
