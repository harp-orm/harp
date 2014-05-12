<?php

namespace CL\Luna\Model;

use CL\Luna\Mapper\AbstractNode;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractModel extends AbstractNode {

    public function __construct(array $fields = null, $state = null)
    {
        $state = $state ?: $this->getDefaultState();

        switch ($state) {
            case self::PERSISTED:
                $this->initializePersisted($fields);
                break;
            case self::PENDING:
                $this->initializePending($fields);
                break;
        }

        parent::__construct($state);

        if ($this->getRepo()->getPolymorphic()) {
            $this->polymorphicClass = get_called_class();
        }
    }

    public function initializePersisted(array $fields = null)
    {
        $fields = $fields !== null ? $fields : $this->getFieldValues();

        $fields = $this->getRepo()->getFields()->loadData($fields);

        $this->setProperties($fields);
    }

    public function initializePending(array $fields = null)
    {
        if ($fields) {
            $this->setProperties($fields);
        }
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

    public function getFieldValues()
    {
        $fields = [];

        foreach ($this->getRepo()->getFieldNames() as $name) {
            $fields[$name] = $this->{$name};
        }

        return $fields;
    }
}
