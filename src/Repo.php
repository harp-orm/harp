<?php

namespace Harp\Harp;

use Harp\Core\Save\AbstractSaveRepo;
use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use Harp\Harp\Query;
use Harp\Query\DB;
use ReflectionProperty;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Repo extends AbstractSaveRepo
{
    private $table;
    private $db = 'default';
    private $fields = array();

    public function __construct($class)
    {
        parent::__construct($class);

        $this->table = $this->getModelReflection()->getShortName();

        $properties = $this->getModelReflection()->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $this->fields []= $property->getName();
        }
    }

    public function getTable()
    {
        $this->initializeOnce();

        return $this->table;
    }

    public function setRootRepo(AbstractRepo $rootRepo)
    {
        if ($rootRepo instanceof Repo) {
            $this->table = $rootRepo->getTable();
        }

        return parent::setRootRepo($rootRepo);
    }

    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    public function getDb()
    {
        $this->initializeOnce();

        return $this->db;
    }

    public function setDb($db)
    {
        $this->db = (string) $db;

        return $this;
    }

    public function getDbInstance()
    {
        return DB::get($this->getDb());
    }

    public function getFields()
    {
        $this->initializeOnce();

        return $this->fields;
    }

    public function setFields(array $items)
    {
        $this->fields = $items;

        return $this;
    }

    /**
     * @param  string $name
     * @return \Harp\Harp\Rel\RelInterface
     */
    public function getRel($name)
    {
        return parent::getRel($name);
    }

    /**
     * @param  string $name
     * @return \Harp\Harp\Rel\RelInterface
     */
    public function getRelOrError($name)
    {
        return parent::getRelOrError($name);
    }

    public function selectAll()
    {
        return new Query\Select($this);
    }

    public function deleteAll()
    {
        return new Query\Delete($this);
    }

    public function updateAll()
    {
        return new Query\Update($this);
    }

    public function insertAll()
    {
        return new Query\Insert($this);
    }

    public function update(Models $models)
    {
        $update = $this->updateAll();

        if ($models->count() > 1) {
            $update
                ->models($models);
        } else {
            $model = $models->getFirst();

            $data = $model->getChanges();
            $this->getSerializers()->serialize($data);

            $update
                ->set($data)
                ->where($this->getPrimaryKey(), $model->getId());
        }

        $update->execute();
    }

    public function delete(Models $models)
    {
        $this
            ->deleteAll()
            ->models($models)
            ->execute();
    }

    public function insert(Models $models)
    {
        $insert = $this->insertAll();

        $insert
            ->models($models)
            ->execute();

        $lastInsertId = $insert->getLastInsertId();

        foreach ($models as $model) {
            $model->setId($lastInsertId);
            $lastInsertId += 1;
        }
    }
}
