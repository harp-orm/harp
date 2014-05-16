<?php

namespace CL\Luna;

use CL\Luna\Query;
use CL\LunaCore\Repo\AbstractRepo;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractDbRepo extends AbstractRepo
{
    const SOFT_DELETE_KEY = 'deletedAt';

    private $table;
    private $softDelete = false;
    private $db = 'default';
    private $primaryKey = 'id';
    private $fields;
    private $fieldDefaults;
    private $asserts;
    private $polymorphic;

    public function getPolymorphic()
    {
        $this->initializeOnce();

        return $this->polymorphic;
    }

    public function setPolymorphic($polymorphic)
    {
        $this->polymorphic = (bool) $polymorphic;

        return $this;
    }

    public function getSoftDelete()
    {
        $this->initializeOnce();

        return $this->softDelete;
    }

    public function setSoftDelete($softDelete)
    {
        $this->softDelete = $softDelete;

        return $this;
    }

    public function getTable()
    {
        $this->initializeOnce();

        return $this->table;
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

    public function getFieldNames()
    {
        $this->initializeOnce();

        return array_keys($this->fields->all());
    }

    public function getFieldDefaults()
    {
        $this->initializeOnce();

        return $this->fieldDefaults;
    }

    public function getFields()
    {
        $this->initializeOnce();

        return $this->fields;
    }

    public function setFields(array $items)
    {
        $this->getFields()->set($items);

        return $this;
    }

    public function getField($name)
    {
        return $this->getFields()->get($name);
    }

    public function selectWithId($key)
    {
        return $this->findAll()->whereKey($key)->loadFirst();
    }

    public function findAll()
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

    public function update(SplObjectStorage $models)
    {
        return $this->updateAll()
            ->setModels($models)
            ->execute();
    }

    public function delete(SplObjectStorage $models)
    {
        return $this->deleteAll()
            ->setModels($models)
            ->execute();
    }

    public function insert(SplObjectStorage $models)
    {
        return $this->insertAll()
            ->setModels($models)
            ->execute();
    }

    public function __construct($modelClass)
    {
        parent::__construct($modelClass);

        $this->fields = new Fields();
        $this->table = $this->getModelReflection()->getShortName();
    }

    public function afterInitialize()
    {
        $allDefaults = $this->getModelReflection()->getDefaultProperties();

        $this->fieldDefaults = array_intersect_key(
            array_replace($this->fields->all(), $allDefaults),
            $this->fields->all()
        );
    }
}
