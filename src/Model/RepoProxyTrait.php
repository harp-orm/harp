<?php

namespace Harp\Harp\Model;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RepoProxyTrait
{
    /**
     * @return string
     */
    abstract public function getModelClass();

    /**
     * @return \Harp\Harp\AbstractModel
     */
    public function find($id, $flags = null)
    {
        $class = $this->getModelClass();

        return $class::find($id, $flags);
    }

    /**
     * @return \Harp\Harp\AbstractModel
     */
    public function findByName($name, $flags = null)
    {
        $class = $this->getModelClass();

        return $class::findByName($name, $flags);
    }

    /**
     * @return \Harp\Harp\Query\Update
     */
    public function updateAll()
    {
        $class = $this->getModelClass();

        return $class::updateAll();
    }

    /**
     * @return \Harp\Harp\Query\Delete
     */
    public function deleteAll()
    {
        $class = $this->getModelClass();

        return $class::deleteAll();
    }

    /**
     * @return \Harp\Harp\Query\Select
     */
    public function selectAll()
    {
        $class = $this->getModelClass();

        return $class::selectAll();
    }

    /**
     * @return \Harp\Harp\Query\Insert
     */
    public function insertAll()
    {
        $class = $this->getModelClass();

        return $class::insertAll();
    }

    /**
     * @return \Harp\Harp\Find
     */
    public function findAll()
    {
        $class = $this->getModelClass();

        return $class::findAll();
    }
}
