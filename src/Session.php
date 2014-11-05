<?php

namespace Harp\Harp\Repo;

use Harp\Harp\Repo;
use Harp\Harp\Config;
use Harp\Query\DB;
use InvalidArgumentException;

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
        return self::$instances[$this->instanceId];
    }

    private static $instances;

    private $instanceId;

    private $configs;

    private $models;

    /**
     * @var DB
     */
    private $db;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    public function __construct(DB $db)
    {
        $this->db = $db;
        $this->configs = new ModelConfigs();
        $this->identityMap = new IdentityMap();
        $this->new = new SplObjectStorage();
        $this->deleted = new SplObjectStorage();
        $this->instanceId = count(self::$instances);

        self::$instances[$this->instanceId] = $this;
    }

    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function add(AbstractModel $model)
    {
        if ($this->identityMap->hasIdentityKey($model)) {
            $model = $this->identityMap->get($model);
        } else {
            $this->new->attach($model);
        }

        $this->attach($model);

        return $this;
    }

    public function attach(AbstractModel $model)
    {
        $model->sessionId = $this->instanceId;

        return $this;
    }

    public function delete(AbstractModel $model)
    {
        if ($this->identityMap->hasIdentityKey($model)) {
            $model = $this->identityMap->get($model);
        } else {
            throw new Exception('Cannot delete unsaved model');
        }

        $this->deleted->attach($model);
        $this->attach($model);
    }

    public function find($class, $id)
    {
        $config = $this->configs->get($class);

        $select = new Select($config);

        return $select->limit(1)->whereKey($id)->getFirst();
    }

    public function selectAll($class)
    {
        $config = $this->configs->get($class);

        return new Select($config);
    }

    public function deleteAll()
    {
        $config = $this->configs->get($class);

        return new Delete($config);
    }

    public function updateAll()
    {
        $config = $this->configs->get($class);

        return new Update($config);
    }
}
