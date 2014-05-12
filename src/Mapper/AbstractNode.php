<?php

namespace CL\Luna\Mapper;

use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractNode
{
    const PENDING = 1;
    const DELETED = 2;
    const PERSISTED = 3;
    const VOID = 4;

    abstract public function getId();
    abstract public function isChanged();
    abstract public function getRepo();

    public $state;

    public function __construct($state = self::PENDING)
    {
        $this->state = $state;
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

    public function setProperties(array $values)
    {
        foreach ($values as $name => $value)
        {
            $this->$name = $value;
        }
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
