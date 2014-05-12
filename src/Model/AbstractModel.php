<?php

namespace CL\Luna\Model;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\MassAssign\AssignNodeInterface;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractModel extends AbstractNode implements AssignNodeInterface {

    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;

    public static function find($key)
    {
        return static::getRepo()->getSelect()->whereKey($key)->loadFirst();
    }

    public static function findAll()
    {
        return static::getRepo()->getSelect();
    }

    public static function deleteAll()
    {
        return static::getRepo()->getDelete();
    }

    public static function updateAll()
    {
        return static::getRepo()->getUpdate();
    }

    public static function insertAll()
    {
        return static::getRepo()->getInsert();
    }

    private $errors;

    public function __construct(array $fields = null, $state = null)
    {
        $state = $state ?: $this->getDefaultState();

        parent::__construct($state);

        switch ($state) {
            case self::PERSISTED:
                $this->initializePersisted($fields);
                break;
            case self::PENDING:
                $this->initializePending($fields);
                break;
            case self::VOID:
                $this->initializeVoid();
                break;
        }
    }

    public function initializePersisted(array $fields = null)
    {
        $fields = $fields !== null ? $fields : $this->getFieldValues();

        $fields = $this->getRepo()->getFields()->loadData($fields);

        $this->setProperties($fields);
        $this->setOriginals($fields);

        return $this;
    }

    public function initializePending(array $fields = null)
    {
        $this->setOriginals($this->getFieldValues());

        if ($this->getRepo()->getPolymorphic()) {
            $this->polymorphicClass = get_called_class();
        }

        if ($fields) {
            $this->setProperties($fields);
        }

        return $this;
    }

    public function initializeVoid()
    {
        $this->setOriginals($this->getFieldValues());

        return $this;
    }

    public function getDefaultState()
    {
        return $this->getId() ? self::PERSISTED : self::PENDING;
    }

    public function getId()
    {
        return $this->{$this->getRepo()->getPrimaryKey()};
    }

    public function setId($id)
    {
        $this->{$this->getRepo()->getPrimaryKey()} = $id;

        return $this;
    }

    public function resetOriginals()
    {
        $this->setOriginals($this->getFieldValues());

        return $this;
    }

    public function setProperties(array $values)
    {
        foreach ($values as $name => $value)
        {
            $this->$name = $value;
        }
    }

    public function getFieldValues()
    {
        $fields = [];

        foreach ($this->getRepo()->getFieldNames() as $name) {
            $fields[$name] = $this->{$name};
        }

        return $fields;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function validate()
    {
        $changes = $this->getChanges();

        if ($this->getUnmapped()) {
            $changes += $this->getUnmapped();
        }

        $this->errors = $this->getRepo()->getAsserts()->execute($changes);

        return $this->isValid();
    }

    public function isEmptyErrors()
    {
        return $this->errors ? $this->errors->isEmpty() : true;
    }

    public function setData(array $data, Closure $yield)
    {
        $rels = $this->getRepo()->getRels()->all();

        $relsData = array_intersect_key($data, $rels);
        $propertiesData = array_diff_key($data, $rels);

        $this->setProperties($propertiesData);

        foreach ($relsData as $relName => $relData) {
            $yield($this->getRepo()->loadLink($this, $relName), $relData);
        }
    }
}
