<?php namespace CL\Luna\Schema;

use ReflectionClass;
use ReflectionProperty;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use CL\Luna\Event\EventDispatcherTrait;
use CL\Luna\Event\ModelEvent;
use CL\Luna\Event\Event;
use CL\Luna\ModelQuery\Delete;
use CL\Luna\ModelQuery\Update;
use CL\Luna\ModelQuery\Select;
use CL\Luna\ModelQuery\Insert;
use CL\Atlas\SQL\SQL;
use CL\Carpo\Asserts;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Schema
{
    const SOFT_DELETE_KEY = 'deletedAt';

    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;

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

    public function dispatchEvent($type, Model $target)
    {
        return $this->getEventListeners()->dispatchEvent($type, $target);
    }

    public function getDeleteQuery()
    {
        if ($this->getSoftDelete())
        {
            $delete = new Update($this);
            $delete
                ->set([self::SOFT_DELETE_KEY => new SQL('CURRENT_TIMESTAMP')])
                ->where([$this->getTable().'.'.self::SOFT_DELETE_KEY => NULL]);
        }
        else
        {
            $delete = new Delete($this);
        }

        return $delete;
    }

    public function getUpdateQuery()
    {
        $update = (new Update($this));

        if ($this->getSoftDelete())
        {
            $update->where([$this->getTable().'.'.self::SOFT_DELETE_KEY => NULL]);
        }

        return $update;
    }

    public function getSelectQuery()
    {
        $select = (new Select($this));

        if ($this->getSoftDelete())
        {
            $select->where([$this->getTable().'.'.self::SOFT_DELETE_KEY => NULL]);
        }

        return $select;
    }

    public function getInsertQuery()
    {
        return new Insert($this);
    }

    public function getQuery($type)
    {
        switch ($type)
        {
            case self::SELECT:
                return $this->getSelectQuery();

            case self::INSERT:
                return $this->getInsertQuery();

            case self::DELETE:
                return $this->getDeleteQuery();

            case self::UPDATE:
                return $this->getUpdateQuery();
        }
    }

    public function newNotLoadedModel()
    {
        return $this->modelReflection->newInstance(NULL, Model::NOT_LOADED);
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
            $this->table = $this->name = strtolower($this->modelReflection->getShortName());

            $this->modelReflection->getMethod('initialize')->invoke(NULL, $this);

            foreach ($this->modelReflection->getTraits() as $trait)
            {
                if ($trait->hasMethod('initialize'))
                {
                    $trait->getMethod('initialize')->invoke(NULL, $this);
                }
            }

            $this->rels->initialize($this);

            $this->fieldDefaults = array_intersect_key(
                array_replace($this->fields->all(), $this->modelReflection->getDefaultProperties()),
                $this->fields->all()
            );

        }
    }
}
