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
class Model implements LinkInterface {

	use DirtyTrackingTrait;
	use UnmappedPropertiesTrait;

	const PENDING = 1;
	const DELETED = 2;
	const PERSISTED = 3;

	private $errors;
	private $state = self::PENDING;
	private $links;

	public function __construct(array $properties = NULL, $loaded = FALSE)
	{
		if ($loaded === TRUE)
		{
			$properties = $properties !== NULL ? $properties : $this->getProperties();

			$properties = $this->getSchema()->getFields()->loadData($properties);

			$this->setProperties($properties);
			$this->setOriginals($properties);

			$this->state = self::PERSISTED;
		}
		else
		{
			$this->setOriginals($this->getProperties());
			$this->setProperties($properties);
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

		return $this;
	}

	public function isPending()
	{
		return $this->state === self::PENDING;
	}

	public function isDeleted()
	{
		return $this->state === self::DELETED;
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

	public function getLinkByName($name)
	{
		$rel = $this->getSchema()->getRel($name);

		if ($this->isEmptyLinks() AND ! $this->getLinks()->contains($rel))
		{
			$this->getLinks()->load($rel, $this);
		}

		return $this->links[$rel];
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
