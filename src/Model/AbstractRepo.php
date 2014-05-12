<?php

namespace CL\Luna\Model;

use CL\Luna\ModelQuery;
use CL\Luna\Mapper;
use CL\Carpo\Asserts;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRepo extends Mapper\AbstractRepo
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
        $this->initializeAllOnce();

        return $this->polymorphic;
    }

    public function setPolymorphic($polymorphic)
    {
        $this->polymorphic = (bool) $polymorphic;

        return $this;
    }

    public function getSoftDelete()
    {
        $this->initializeAllOnce();

        return $this->softDelete;
    }

    public function setSoftDelete($softDelete)
    {
        $this->softDelete = $softDelete;

        return $this;
    }

    public function getTable()
    {
        $this->initializeAllOnce();

        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    public function getDb()
    {
        $this->initializeAllOnce();

        return $this->db;
    }

    public function setDb($db)
    {
        $this->db = (string) $db;

        return $this;
    }

    public function getFieldNames()
    {
        $this->initializeAllOnce();

        return array_keys($this->fields->all());
    }

    public function getFieldDefaults()
    {
        $this->initializeAllOnce();

        return $this->fieldDefaults;
    }

    public function getFields()
    {
        $this->initializeAllOnce();

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

    public function getAsserts()
    {
        $this->initializeAllOnce();

        return $this->asserts;
    }

    public function setAsserts(array $asserts)
    {
        $this->initializeAllOnce();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    public function find($key)
    {
        return $this->findAll()->whereKey($key)->loadFirst();
    }

    public function findAll()
    {
        return new ModelQuery\Select($this);
    }

    public function deleteAll()
    {
        return new ModelQuery\Delete($this);
    }

    public function updateAll()
    {
        return new ModelQuery\Update($this);
    }

    public function insertAll()
    {
        return new ModelQuery\Insert($this);
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
        $this->asserts = new Asserts();
        $this->table = $this->getModelReflection()->getShortName();
    }

    public function initializeAll()
    {
        parent::initializeAll();

        $allDefaults = $this->getModelReflection()->getDefaultProperties();

        $this->fieldDefaults = array_intersect_key(
            array_replace($this->fields->all(), $allDefaults),
            $this->fields->all()
        );
    }
}
