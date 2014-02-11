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

	public function __construct(array $attributes = NULL, $loaded = FALSE)
	{
		if ($loaded === TRUE)
		{
			$attributes = $attributes !== NULL ? $attributes : $this->getAttributes();

			$attributes = Arr::invokeObjects($attributes, static::getFields(), 'load');

			$this->setAttributes($attributes);
			$this->setOriginals($attributes);

			$this->isLoaded = TRUE;
		}
		else
		{
			$this->setOriginals($this->getAttributes());
			$this->setAttributes($attributes);
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
		return $this->{static::getPrimaryKey()};
	}

	public function isLoaded()
	{
		return $this->isLoaded;
	}

	public function setAttributes(array $values)
	{
		foreach ($values as $name => $value)
		{
			$this->$name = $value;
		}
	}

	public function save()
	{
		$this->validate();

		if ($this->getSchema()->dipatchModelEvent(ModelEvent::SAVE, $this) AND $this->isChanged())
		{
			$changes = Arr::invokeObjects($this->getChanges(), static::getFields(), 'save');
			$this->insertOrUpdate($changes);
		}

		$this->getSchema()->dipatchModelEvent(ModelEvent::AFTER_SAVE, $this);
	}

	public function getAttributes()
	{
		$values = array();
		$fieldNames = array_keys(static::getFields());

		foreach ($fieldNames as $name)
		{
			$values[$name] = $this->$name;
		}

		return $values;
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
			$this->rels[$name] = static::getRels()[$name]->load($this);
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
		if ( ! $this->errors)
		{
			$this->errors = new Errors();
		}

		return $this->errors;
	}

	public function validate()
	{
		$this->getErrors()->validateChanges($this->getChanges(), static::getValidators());

		return $this->isValid();
	}

	public function isValid()
	{
		return $this->getErrors()->isEmpty();
	}
}
