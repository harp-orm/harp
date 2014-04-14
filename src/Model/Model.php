<?php namespace CL\Luna\Model;

use CL\Luna\Mapper\AbstractNode;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Model extends AbstractNode {

    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;

    private $errors;

    public function __construct(array $fields = NULL, $state = self::PENDING)
    {
        parent::__construct($state);

        if ($state === self::PERSISTED)
        {
            $fields = $fields !== NULL ? $fields : $this->getFieldValues();

            $fields = $this->getSchema()->getFields()->loadData($fields);

            $this->setProperties($fields);
            $this->setOriginals($fields);
        }
        elseif ($state === self::PENDING)
        {
            $this->setOriginals($this->getFieldValues());
            if ($fields)
            {
                $this->setProperties($fields);
            }
        }
        else
        {
            $this->setOriginals($this->getFieldValues());
        }
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
}
