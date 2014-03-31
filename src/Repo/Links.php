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
    public function add($name, AbstractLink $link)
    {
        $this->items[$name] = $link;

        return $this;
    }

    public function updateAll(Model $parent)
    {
        if ($this->items) {
            foreach ($this->items as $link) {
                $link->update($parent);
            }
        }

        return $this;
    }

    public function getAllModels()
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
