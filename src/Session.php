<?php

namespace Harp\Harp;

use Harp\Query\DB;
use Harp\Harp\Query\Select;
use Harp\Harp\Query\Delete;
use Harp\Harp\Query\Update;
use Harp\IdentityMap\IdentityMap;
use InvalidArgumentException;
use SplObjectStorage;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Session
{
    public static function getInstance($instanceId)
    {
        return self::$instances[$instanceId];
    }

    private static $instances;

    private $instanceId;

    private $configContainer;

    private $models;

    /**
     * @var DB
     */
    private $db;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    public function __construct(DB $db, array $aliases = array())
    {
        $this->db = $db;

        $this->configContainer = new ConfigContainer($this, $aliases);

        $this->identityMap = new IdentityMap(function (Model $model) {
            return get_class($model).':'.$model->getId();
        });

        $this->new = new SplObjectStorage();
        $this->deleted = new SplObjectStorage();
        $this->instanceId = count(self::$instances) + 1;

        self::$instances[$this->instanceId] = $this;
    }

    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function getConfigContainer()
    {
        return $this->configContainer;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getConfig($class)
    {
        return $this->configContainer->get($class);
    }

    public function add(Model $model)
    {
        if ($model->getId()) {
            $model = $this->identityMap->get($model);
        } else {
            $this->new->attach($model);
        }

        $model->setSession($this);

        return $model;
    }

    public function delete(Model $model)
    {
        if ($model->getId()) {
            $model = $this->identityMap->get($model);
        } else {
            throw new Exception('Cannot delete unsaved model');
        }

        $this->deleted->attach($model);
        $model->setSession($this);
    }

    public function getModel($class, $id)
    {
        $config = $this->getConfig($class);

        $select = new Select($this, $config);

        return $select->whereKey($id)->fetchFirst();
    }

    public function getSelect($class)
    {
        $config = $this->getConfig($class);

        return new Select($this, $config);
    }

    public function getDelete($class)
    {
        $config = $this->getConfig($class);

        return new Delete($this, $config);
    }

    public function getUpdate($class)
    {
        $config = $this->getConfig($class);

        return new Update($this, $config);
    }

    public function getInsert($class)
    {
        $config = $this->getConfig($class);

        return new Update($this, $config);
    }
}
