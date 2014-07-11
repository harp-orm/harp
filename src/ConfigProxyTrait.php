<?php

namespace Harp\Harp;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait ConfigProxyTrait
{
    /**
     * @return Config
     */
    abstract public function getConfig();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getConfig()->getName();
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->getConfig()->getModelClass();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConfig()->getTable();
    }

    /**
     * @return string
     */
    public function getDb()
    {
        return $this->getConfig()->getDb();
    }

    /**
     * @return \Harp\Harp\Repo\ReflectionModel
     */
    public function getReflectionModel()
    {
        return $this->getConfig()->getReflectionModel();
    }

    /**
     * @return ReflectionClass
     */
    public function getRootReflectionClass()
    {
        return $this->getConfig()->getRootReflectionClass();
    }

    /**
     * @return boolean
     */
    public function getSoftDelete()
    {
        return $this->getConfig()->getSoftDelete();
    }

    /**
     * @return boolean
     */
    public function getInherited()
    {
        return $this->getConfig()->getInherited();
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->getConfig()->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function getNameKey()
    {
        return $this->getConfig()->getNameKey();
    }

    /**
     * @return Rel\AbstractRel[]
     */
    public function getRels()
    {
        return $this->getConfig()->getRels();
    }

    /**
     * @return Rel\AbstractRel
     */
    public function getRel($name)
    {
        return $this->getConfig()->getRel($name);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->getConfig()->getFields();
    }

    /**
     * @return Rel\AbstractRel
     * @throws InvalidArgumentException If rel does not exist
     */
    public function getRelOrError($name)
    {
        return $this->getConfig()->getRelOrError($name);
    }

    /**
     * @return \Harp\Validate\Asserts
     */
    public function getAsserts()
    {
        return $this->getConfig()->getAsserts();
    }

    /**
     * @return \Harp\Serializer\Serializers
     */
    public function getSerializers()
    {
        return $this->getConfig()->getSerializers();
    }

    /**
     * @return Repo\EventListeners
     */
    public function getEventListeners()
    {
        return $this->getConfig()->getEventListeners();
    }

    /**
     * @return boolean
     */
    public function getInitialized()
    {
        return $this->getConfig()->getInitialized();
    }

    /**
     * @return boolean
     */
    public function isModel(AbstractModel $model)
    {
        return $this->getConfig()->isModel($model);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function assertModel(AbstractModel $model)
    {
        return $this->getConfig()->assertModel($model);
    }
}
