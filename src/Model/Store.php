<?php

namespace CL\Luna\Model;

use CL\Luna\Mapper\StoreInterface;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\NodeEvent;
use CL\Luna\ModelQuery;
use CL\Luna\Util\Arr;
use CL\Carpo\Asserts;
use ReflectionClass;
use ReflectionProperty;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Store implements StoreInterface
{
    const SOFT_DELETE_KEY = 'deletedAt';

    private $name;
    private $modelClass;
    private $modelReflection;
    private $table;
    private $softDelete = FALSE;
    private $db = 'default';
    private $primaryKey = 'id';
    private $fields;
    private $fieldDefaults;
    private $rels;
    private $asserts;
    private $configurationLoaded;
    private $cascadeRels;
    private $polymorphic;

    public function getName()
    {
        $this->lazyLoadConfiguration();

        return $this->name;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getModelReflection()
    {
        $this->lazyLoadConfiguration();

        return $this->modelReflection;
    }

    public function getPrimaryKey()
    {
        $this->lazyLoadConfiguration();

        return $this->primaryKey;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getPolymorphic()
    {
        $this->lazyLoadConfiguration();

        return $this->polymorphic;
    }

    public function setPolymorphic($polymorphic)
    {
        $this->polymorphic = (bool) $polymorphic;

        return $this;
    }

    public function getSoftDelete()
    {
        $this->lazyLoadConfiguration();

        return $this->softDelete;
    }

    public function setSoftDelete($softDelete)
    {
        $this->softDelete = $softDelete;

        return $this;
    }

    public function getTable()
    {
        $this->lazyLoadConfiguration();

        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    public function getDb()
    {
        $this->lazyLoadConfiguration();

        return $this->db;
    }

    public function setDb($db)
    {
        $this->db = (string) $db;

        return $this;
    }

    public function getFieldNames()
    {
        $this->lazyLoadConfiguration();

        return array_keys($this->fields->all());
    }

    public function getFieldDefaults()
    {
        $this->lazyLoadConfiguration();

        return $this->fieldDefaults;
    }

    public function getFields()
    {
        $this->lazyLoadConfiguration();

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

    public function getRels()
    {
        $this->lazyLoadConfiguration();

        return $this->rels;
    }

    public function setRels(array $rels)
    {
        $this->getRels()->set($rels);

        return $this;
    }

    public function getRel($name)
    {
        return $this->getRels()->get($name);
    }

    public function getAsserts()
    {
        $this->lazyLoadConfiguration();

        return $this->asserts;
    }

    public function setAsserts(array $asserts)
    {
        $this->lazyLoadConfiguration();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    public function getEventListeners()
    {
        $this->lazyLoadConfiguration();

        return $this->eventListeners;
    }

    public function setEventBeforeDelete($callback)
    {
        $this->getEventListeners()->addBefore(AbstractNode::DELETE, $callback);

        return $this;
    }

    public function setEventAfterDelete($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::DELETE, $callback);

        return $this;
    }

    public function setEventBeforeSave($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::SAVE, $callback);

        return $this;
    }

    public function setEventAfterSave($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::SAVE, $callback);

        return $this;
    }

    public function setEventBeforeInsert($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::INSERT, $callback);

        return $this;
    }

    public function setEventAfterInsert($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::INSERT, $callback);

        return $this;
    }

    public function setEventBeforeUpdate($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::UPDATE, $callback);

        return $this;
    }

    public function setEventAfterUpdate($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::UPDATE, $callback);

        return $this;
    }

    public function setEventAfterLoad($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::LOAD, $callback);

        return $this;
    }

    public function dispatchEvent($event, Model $target)
    {
        return $this->getEventListeners()->dispatchEvent($event, $target);
    }

    public function hasEvent($event)
    {
        return $this->getEventListeners()->hasEvent($event);
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

    public function dispatchBeforeEvent($models, $event)
    {
        $this->lazyLoadConfiguration();

        $this->getEventListeners()->dispatchBeforeEvent($models, $event);
    }

    public function dispatchAfterEvent($models, $event)
    {
        $this->lazyLoadConfiguration();

        $this->getEventListeners()->dispatchAfterEvent($models, $event);
    }

    public function newInstance($fields = null, $status = AbstractNode::PENDING)
    {
        $this->lazyLoadConfiguration();

        return $this->modelReflection->newInstance($fields, $status);
    }

    public function getCascadeRels()
    {
        $this->lazyLoadConfiguration();

        return $this->cascadeRels;
    }

    function __construct($className)
    {
        $this->modelClass = $className;
    }

    public function lazyLoadConfiguration()
    {
        if ($this->configurationLoaded === NULL)
        {
            $this->configurationLoaded = TRUE;

            $this->asserts = new Asserts();
            $this->fields = new Fields();
            $this->eventListeners = new EventListeners();
            $this->rels = new Rels();

            $this->modelReflection = new ReflectionClass($this->getModelClass());
            $this->table = $this->name = $this->modelReflection->getShortName();

            $this->initialize();

            foreach ((new ReflectionClass($this))->getTraits() as $trait) {
                if ($trait->hasMethod('initializeTrait')) {
                    $trait->getMethod('initializeTrait')->invoke(null, $this);
                }
            }

            $this->fieldDefaults = array_intersect_key(
                array_replace($this->fields->all(), $this->modelReflection->getDefaultProperties()),
                $this->fields->all()
            );

            // $this->cascadeRels = Arr::filterInvoke($this->rels->all(), 'getCascade');
        }
    }
}
