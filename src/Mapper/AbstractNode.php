<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Util;
use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractNode
{
    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;

    const PENDING = 1;
    const DELETED = 2;
    const PERSISTED = 3;
    const VOID = 4;

    abstract public function getId();
    abstract public function getRepo();

    public $state;
    private $errors;

    public function __construct($state = self::PENDING)
    {
        $this->state = $state;
        $this->setOriginals($this->getProperties());
    }

    public function setStateNotVoid()
    {
        $this->state = $this->getId() ? self::PERSISTED : self::PENDING;

        return $this;
    }

    public function setStateVoid()
    {
        $this->state = self::VOID;

        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function isPersisted()
    {
        return $this->state === self::PERSISTED;
    }

    public function isPending()
    {
        return $this->state === self::PENDING;
    }

    public function isDeleted()
    {
        return $this->state === self::DELETED;
    }

    public function isVoid()
    {
        return $this->state === self::VOID;
    }

    public function delete()
    {
        $this->state = self::DELETED;

        return $this;
    }

    public function dispatchEvent($event)
    {
        $this->getRepo()->dispatchEvent($event, $this);

        return $this;
    }

    public function getProperties()
    {
        return Util::getPublicProperties($this);
    }

    public function setProperties(array $values)
    {
        foreach ($values as $name => $value)
        {
            $this->$name = $value;
        }
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

        return $this->isEmptyErrors();
    }

    public function isEmptyErrors()
    {
        return $this->errors ? $this->errors->isEmpty() : true;
    }
}
