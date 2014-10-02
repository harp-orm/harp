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
    private static $instances;

    private $id;

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
        $this->identityMap = new IdentityMap();
        $this->new = new SplObjectStorage();
        $this->deleted = new SplObjectStorage();
        $this->instanceId = count(self::$instances);

        self::$instances[$this->instanceId] = $this;
    }

    /**
     * @return DB
     */
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

    public function attach($model)
    {
        $model->sessionId = $this->instanceId;

        return $this;
    }




    public function getModelClass($name)
    {
        return ($this->alias AND isset($this->alias[$name]))
            ? $this->alias[$name]
            : $name;
    }

    public function getConfig($class)
    {
        if (! isset($this->repos[$class])) {
            $class = $this->getModelClass();

            $this->repos[$class] = new Repo(new Config($class));
        }

        return $this->repos[$class];
    }

    public function getSelect($class)
    {
        $repo = $this->getRepo($class);

        return new Select($repo);
    }

    public function delete($class)
    {
        $repo = $this->getRepo($class);

        return new Select($repo);
    }
}
