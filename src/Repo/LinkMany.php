<?php namespace CL\Luna\Repo;

use CL\Luna\Util\Storage;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Model\Model;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMany extends AbstractLink
{
    protected $models;
    protected $original;

    public function __construct(AbstractRel $rel, array $models)
    {
        parent::__construct($rel);

        $this->models = new SplObjectStorage();

        $this->set($models);

        $this->original = clone $this->models;
    }

    public function set(array $models)
    {
        array_walk($models, [$this, 'add']);

        return $this;
    }

    public function count()
    {
        return $this->models->count();
    }

    public function clear()
    {
        $this->models = new SplObjectStorage();
    }

    public function add(Model $model)
    {
        $this->models->attach($model);
    }

    public function remove(Model $model)
    {
        $this->models->remove($model);
    }

    public function isEmpty()
    {
        return count($this->models) === 0;
    }

    public function has(Model $model)
    {
        return $this->models->contains();
    }

    public function all()
    {
        return $this->models;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function getOriginalIds()
    {
        return Storage::invoke($this->original, 'getId');
    }

    public function getIds()
    {
        return Storage::invoke($this->models, 'getId');
    }

    public function getAdded()
    {
        $current = clone $this->models;
        $current->removeAll($this->original);
        return $current;
    }

    public function getRemoved()
    {
        $current = clone $this->original;
        $current->removeAll($this->models);
        return $current;
    }

    public function getAll()
    {
        $current = clone $this->models;
        $current->addAll($this->original);
        return $current;
    }
}
