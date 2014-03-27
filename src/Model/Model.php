<?php namespace CL\Luna\Model;

use CL\Luna\Util\Arr;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Event\ModelEvent;
use CL\Luna\Schema\Query\Update;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model {

    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;

    const PENDING = 1;
    const DELETED = 2;
    const PERSISTED = 3;
    const NOT_LOADED = 4;

    private $errors;
    private $state;
    private $links;

    public function __construct(array $fields = NULL, $state = self::PENDING)
    {
        $this->state = $state;

        if ($state === self::PERSISTED)
        {
            $fields = $fields !== NULL ? $fields : $this->getFieldValues();

            $fields = $this->getSchema()->getFields()->loadData($fields);

            $this->setFieldValues($fields);
            $this->setOriginals($fields);
        }
        elseif ($state === self::PENDING)
        {
            $this->setOriginals($this->getFieldValues());
            if ($fields)
            {
                $this->setFieldValues($fields);
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

    public function setStateLoaded()
    {
        $this->state = $this->getId() ? self::PERSISTED : self::PENDING;

        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function resetOriginals()
    {
        $this->setOriginals($this->getFieldValues());

        return $this;
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

    public function isNotLoaded()
    {
        return $this->state === self::NOT_LOADED;
    }

    public function setFieldValues(array $values)
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

    public function dispatchEvent($type)
    {
        $this->getSchema()->dispatchEvent($type, $this);

        return $this;
    }

    public function setLink(AbstractRel $rel, LinkInterface $link)
    {
        $this->getLinks()->attach($rel, $link);
    }

    public function isEmptyLinks()
    {
        return ($this->links === NULL OR empty($this->links));
    }

    public function getLinks()
    {
        if ($this->links === NULL)
        {
            $this->links = new Links();
        }

        return $this->links;
    }

    public function getLink(AbstractRel $rel)
    {
        if ( ! $this->getLinks()->contains($rel))
        {
            $this->getLinks()->load($rel, $this);
        }

        return $this->getLinks()->offsetGet($rel);
    }

    public function getLinkByName($name)
    {
        $rel = $this->getSchema()->getRel($name);

        return $this->getLink($rel);
    }

    public function setLinkByName($name, LinkInterface $link)
    {
        $rel = $this->getSchema()->getRel($name);

        $this->setLink($rel, $link);

        return $this;
    }

    public function updateLinks()
    {
        if ( ! $this->isEmptyLinks())
        {
            $this->getLinks()->update($this);
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

        $this->errors = $this->getSchema()->getAsserts()->execute($changes);

        return $this->isValid();
    }

    public function isEmptyErrors()
    {
        return $this->errors ? $this->errors->isEmpty() : true;
    }
}
