<?php namespace CL\Luna\Model;

use CL\Luna\Util\Arr;
use CL\Luna\Event\ModelEvent;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model {

	use DirtyTrackingTrait;

	protected $errors;
	protected $rels;
	protected $isLoaded = FALSE;
	protected $unmapped;

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

	public function __get($name)
	{
		return isset($this->unmapped[$name]) ? $name : NULL;
	}

	public function __set($name, $value)
	{
		$this->unmapped[$name] = $value;
		return $this;
	}

	public function __isset($name)
	{
		return isset($this->unmapped[$name]);
	}

	public function getId()
	{
		return $this->{$this->getSchema()->getPrimaryKey()};
	}

	public function isLoaded()
	{
		return $this->isLoaded;
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
		$this->validate();

		if ($this->getSchema()->dipatchModelEvent(ModelEvent::SAVE, $this))
		{
			if ($this->rels)
			{
				$this->getSchema()->getRels()->setArray($this, $this->rels);
			}

			if ($this->isChanged())
			{
				$changes = $this->getSchema()->getFields()->saveData($this->getChanges());
				$this->insertOrUpdate($changes);
			}

			if ($this->rels)
			{
				$this->getSchema()->getRels()->saveArray($this, $this->rels);
			}
		}

		$this->getSchema()->dipatchModelEvent(ModelEvent::AFTER_SAVE, $this);
	}


	public function insertOrUpdate(array $attributes)
	{
		$query = $this->isLoaded()
			? static::update()->whereKey($this->getId())
			: static::insert();

		$query
			->set($attributes)
			->execute();
	}

	public function getRel($name)
	{
		if ( ! isset($this->rels[$name]))
		{
			$this->rels[$name] = $this->getSchema()->getRel($name)->load($this);
		}

		return $this->rels[$name];
	}

	public function setRel($name, $value)
	{
		$this->rels[$name] = $value;

		return $this;
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
