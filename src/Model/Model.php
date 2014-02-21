<?php namespace CL\Luna\Model;

use CL\Luna\Util\Arr;
use CL\Luna\Event\ModelEvent;
use CL\Luna\EntityManager\EntityManager;
use CL\Luna\EntityManager\RelContent;
use CL\Luna\Schema\Query\Update;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model {

	use DirtyTrackingTrait;
	use UnmappedPropertiesTrait;

	private $errors;
	private $relContents;
	private $isLoaded = FALSE;
	private $isDeleted = FALSE;
	private $isSaved = FALSE;

	public function __construct(array $properties = NULL, $loaded = FALSE)
	{
		if ($loaded === TRUE)
		{
			$properties = $properties !== NULL ? $properties : $this->getProperties();

			$properties = $this->getSchema()->getFields()->loadData($properties);

			$this->setProperties($properties);
			$this->setOriginals($properties);

			$this->isLoaded = TRUE;
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
		$this->isLoaded = TRUE;

		return $this;
	}

	public function isLoaded()
	{
		return $this->isLoaded;
	}

	public function isDeleted()
	{
		return $this->isDeleted;
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
			$this->isSaving = TRUE;
		}

		return $this;
	}

	public function preserve()
	{
		if ($this->getSchema()->dipatchModelEvent(ModelEvent::PRESERVE, $this))
		{
			$this->setOriginals($this->getProperties());
			$this->isSaving = FALSE;
		}

		return $this;
	}

	public function delete()
	{
		if ($this->getSchema()->dipatchModelEvent(ModelEvent::DELETE, $this))
		{
			$this->isDeleted = TRUE;
		}

		return $this;
	}

	public function restore()
	{
		(new Update($this->getSchema()))
			->whereKey($this->getId())
			->set([Schema::SOFT_DELETE_KEY => NULL])
			->execute();

		return $this;
	}

	public function getLink($relName)
	{
		$rel = $this->getSchema()->getRel($relName);
		return EntityManager::getInstance()->loadLink($this, $rel)->getContent();
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
