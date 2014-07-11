<?php

namespace Harp\Harp\Repo;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;

/**
 * A class holding all the links for a given model. LinkMap holds Links objects.
 * Allows retrieving all the linked models recursively.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Links
{
    /**
     * @var AbstractModel
     */
    private $model;

    /**
     * @var AbstractLink[]
     */
    private $items = [];

    /**
     * @param AbstractModel $model
     */
    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return AbstractModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return AbstractLink[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param  AbstractLink $link
     * @return Links        $this
     */
    public function add(AbstractLink $link)
    {
        $name = $link->getRel()->getName();

        $this->items[$name] = $link;

        return $this;
    }

    /**
     * Get all of the linked models
     *
     * @return Models
     */
    public function getModels()
    {
        $all = new Models();

        foreach ($this->items as $item) {
            $all->addAll($item->getCurrentAndOriginal());
        }

        return $all;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * @param  string            $name
     * @return AbstractLink|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
    }
}
