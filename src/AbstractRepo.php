<?php

namespace Harp\Harp;

use Harp\Core\Save\AbstractSaveRepo;
use Harp\Core\Repo;
use Harp\Core\Model\Models;
use Harp\Harp\Rel\RelInterface;
use Harp\Harp\Query;
use Harp\Query\DB;
use ReflectionProperty;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRepo extends AbstractSaveRepo
{
    private $table;
    private $db = 'default';
    private $fields = array();

    public function __construct($modelClass)
    {
        parent::__construct($modelClass);

        $properties = $this->getModelReflection()->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $this->fields []= $property->getName();
        }

        $this->table = $this->getModelReflection()->getShortName();
    }

    public function getTable()
    {
        $this->initializeOnce();

        return $this->table;
    }

    public function setRootRepo(Repo\AbstractRepo $rootRepo)
    {
        if ($rootRepo instanceof AbstractRepo) {
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
     * @return RelInterface
     */
    public function getRel($name)
    {
        return parent::getRel($name);
    }

    /**
     * @param  string $name
     * @return RelInterface
     */
    public function getRelOrError($name)
    {
        return parent::getRelOrError($name);
    }

    public function findAll()
    {
        return new Find($this);
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
            $update
                ->set($this->serializeModel($model->getChanges()))
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
