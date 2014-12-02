<?php

namespace Harp\Harp;

use SplObjectStorage;
use Iterator;
use Countable;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Models implements Iterator, Countable
{
    private $models;

    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getFirst()
    {
        $this->rewind();

        return $this->current() ?: $this->loader->getVoidModel();
    }

    public function getNext()
    {
        $this->next();

        return $this->current() ?: $this->loader->getVoidModel();
    }

    public function getSelect()
    {
        return $this->loader->getSelect();
    }

    public function getIds()
    {
        return Objects::invoke($this->all(), 'getId');
    }

    public function has(Model $model)
    {
        return $this->all()->contains($model);
    }

    public function count()
    {
        return count($this->all());
    }

    public function toArray()
    {
        return iterator_to_array($this->all());
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function all()
    {
        if (null === $this->models) {
            $this->models = $this->loader->getModels();
        }

        return $this->models;
    }

    public function current()
    {
        return $this->all()->current();
    }

    public function key()
    {
        return $this->all()->key();
    }

    public function next()
    {
        $this->all()->next();
    }

    public function rewind()
    {
        $this->all()->rewind();
    }

    public function valid()
    {
        return $this->all()->valid();
    }
}
