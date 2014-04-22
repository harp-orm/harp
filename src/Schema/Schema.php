<?php namespace CL\Luna\Schema;

use CL\Luna\Mapper\SchemaInterface;
use CL\Luna\Mapper\AbstractNode;
use ReflectionClass;
use ReflectionProperty;
use SplObjectStorage;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use CL\Luna\ModelQuery;
use CL\Carpo\Asserts;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Schema implements SchemaInterface
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

    public function dispatchEvent($event, Model $target)
    {
        return $this->getEventListeners()->dispatchEvent($event, $target);
    }

    public function hasEvent($event)
    {
        return $this->getEventListeners()->hasEvent($event);
    }

    public function getDeleteQuery()
    {
        if ($this->getSoftDelete())
        {
            $delete = new ModelQuery\SoftDelete($this);
        }
        else
        {
            $delete = new ModelQuery\Delete($this);
        }

        return $delete;
    }

    public function getUpdateQuery()
    {
        $update = new ModelQuery\Update($this);

        if ($this->getSoftDelete())
        {
            $update->where([$this->getTable().'.'.self::SOFT_DELETE_KEY => NULL]);
        }

        return $update;
    }

    public function getSelectQuery()
    {
        $select = new ModelQuery\Select($this);

        if ($this->getSoftDelete())
        {
            $select->where([$this->getTable().'.'.self::SOFT_DELETE_KEY => NULL]);
        }

        return $select;
    }

    public function getInsertQuery()
    {
        return new ModelQuery\Insert($this);
    }

    public function update(SplObjectStorage $models)
    {
        return $this
            ->getUpdateQuery()
            ->setModels($models)
            ->execute();
    }

    public function delete(SplObjectStorage $models)
    {
        return $this
            ->getDeleteQuery()
            ->setModels($models)
            ->execute();
    }

    public function insert(SplObjectStorage $models)
    {
        return $this
            ->getInsertQuery()
            ->setModels($models)
            ->execute();
    }

    public function select(array $conditions)
    {
        return $this
            ->getSelectQuery()
            ->where($conditions)
            ->load();
    }

    public function dispatchBeforeEvent(SplObjectStorage $models, $event)
    {
        $this->lazyLoadConfiguration();

        $this->getEventListeners()->dispatchBeforeEvent($models, $event);
    }

    public function dispatchAfterEvent(SplObjectStorage $models, $event)
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

    function __construct($class_name)
    {
        $this->modelClass = $class_name;
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

            $this->modelReflection->getMethod('initialize')->invoke(NULL, $this);

            foreach ($this->modelReflection->getTraits() as $trait)
            {
                if ($trait->hasMethod('initialize'))
                {
                    $trait->getMethod('initialize')->invoke(NULL, $this);
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
