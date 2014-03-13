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

    public function __construct(array $properties = NULL, $state = self::PENDING)
    {
        $this->state = $state;

        if ($state === self::PERSISTED)
        {
            $properties = $properties !== NULL ? $properties : $this->getProperties();

            $properties = $this->getSchema()->getFields()->loadData($properties);

            $this->setProperties($properties);
            $this->setOriginals($properties);
        }
        elseif ($state === self::PENDING)
        {
            $this->setOriginals($this->getProperties());
            if ($properties)
            {
                $this->setProperties($properties);
            }
        }
        else
        {
            $this->setOriginals($this->getProperties());
        }
    }

    public function getId()
    {
        return $this->{$this->getSchema()->getPrimaryKey()};
    }

    public function setInserted($id)
    {
        $this->{$this->getSchema()->getPrimaryKey()} = $id;
        $this->setOriginals($this->getProperties());
        $this->state = self::PERSISTED;

        return $this;
    }

    public function setStateLoaded()
    {
        $this->state = $this->getId() ? self::PERSISTED : self::PENDING;

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

    public function setProperties(array $values)
    {
        foreach ($values as $name => $value)
        {
            $this->$name = $value;
        }
    }

    public function getProperties()
    {
        $properties = [];
        foreach ($this->getSchema()->getPropertyNames() as $name)
        {
            $properties[$name] = $this->{$name};
        }
        return $properties;
    }

    public function save()
    {
        if ($this->getSchema()->dipatchModelEvent(ModelEvent::SAVE, $this))
        {
            // $this->isSaved = TRUE;
        }

        return $this;
    }

    public function persist()
    {
        if ($this->getSchema()->dipatchModelEvent(ModelEvent::PRESERVE, $this))
        {
            $this->setOriginals($this->getProperties());
            // $this->isSaved = FALSE;
        }

        return $this;
    }

    public function delete()
    {
        if ($this->getSchema()->dipatchModelEvent(ModelEvent::DELETE, $this))
        {
            $this->state = self::DELETED;
        }

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
            $this->links = new Links($this);
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
        $this->errors = $this->getSchema()->getValidators()->executeArray($this->getChanges());

        return $this->isValid();
    }

    public function isValid()
    {
        return $this->getErrors() ? $this->getErrors()->isEmpty() : TRUE;
    }
}
