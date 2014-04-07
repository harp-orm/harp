<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Util\Collection;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Links extends Collection
{
    protected $model;

    function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function add($name, AbstractLink $link)
    {
        $this->items[$name] = $link;

        return $this;
    }

    public function updateAll()
    {
        if ($this->items) {
            foreach ($this->items as $link) {
                $link->getRel()->update($this->model, $link);
            }
        }

        return $this;
    }

    public function getModelsRecursive()
    {
        $models = new SplObjectStorage();

        if ($this->items) {
            foreach ($this->items as $link) {
                $models->addAll($link->getAll());
            }
        }

        return $models;
    }
}
