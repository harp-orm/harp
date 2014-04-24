<?php

namespace CL\Luna\Model;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\Repo;
use CL\Luna\MassAssign\AssignNodeInterface;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Model extends AbstractNode implements AssignNodeInterface {

    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;

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
            case self::NOT_LOADED:
                $this->initializeNotLoaded();
                break;
        }
    }

    public function initializePersisted(array $fields = null)
    {
        $fields = $fields !== null ? $fields : $this->getFieldValues();

        $fields = $this->getSchema()->getFields()->loadData($fields);

        $this->setProperties($fields);
        $this->setOriginals($fields);

        return $this;
    }

    public function initializePending(array $fields = null)
    {
        $this->setOriginals($this->getFieldValues());
        if ($this->getSchema()->getPolymorphic()) {
            $this->polymorphicClass = get_called_class();
        }
        if ($fields) {
            $this->setProperties($fields);
        }

        return $this;
    }

    public function initializeNotLoaded()
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
        return $this->{$this->getSchema()->getPrimaryKey()};
    }

    public function setId($id)
    {
        $this->{$this->getSchema()->getPrimaryKey()} = $id;

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
        foreach ($this->getSchema()->getFieldNames() as $name)
        {
            $fields[$name] = $this->{$name};
        }
        return $fields;
    }

    public function delete()
    {
        $this->state = self::DELETED;

        return $this;
    }

    public function dispatchEvent($event)
    {
        $this->getSchema()->dispatchEvent($event, $this);

        return $this;
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

        $this->errors = $this->getSchema()->getAsserts()->execute($changes);

        return $this->isValid();
    }

    public function isEmptyErrors()
    {
        return $this->errors ? $this->errors->isEmpty() : true;
    }

    public function setData(array $data, Closure $yield)
    {
        $rels = $this->getSchema()->getRels()->all();

        $relsData = array_intersect_key($data, $rels);
        $propertiesData = array_diff_key($data, $rels);

        $this->setProperties($propertiesData);

        foreach ($relsData as $relName => $relData) {
            $yield($this->loadRelLink($relName), $relData);
        }
    }
}
