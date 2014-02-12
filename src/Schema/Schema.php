<?php namespace CL\Luna\Schema;

use ReflectionClass;
use ReflectionProperty;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use CL\Luna\Event\EventDispatcherTrait;
use CL\Luna\Event\ModelEvent;
use CL\Luna\Event\Event;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Schema
{
	private $name;
	private $modelClass;
	private $table;
	private $db = 'default';
	private $primaryKey = 'id';
	private $fields;
	private $propertyNames;
	private $rels;
	private $validators;
	private $configurationLoaded;

	public function getName()
	{
		$this->lazyLoadConfiguration();

		return $this->name;
	}

	public function getModelClass()
	{
		return $this->modelClass;
	}

	public function getPrimaryKey()
	{
		$this->lazyLoadConfiguration();

		return $this->primaryKey;
	}

	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;

		return $this;
	}

	public function getTable()
	{
		$this->lazyLoadConfiguration();

		return $this->table;
	}

	public function setTable($table)
	{
		$this->table = (string) $table;

		return $this;
	}

	public function getDb()
	{
		$this->lazyLoadConfiguration();

		return $this->db;
	}

	public function setDb($db)
	{
		$this->db = (string) $db;

		return $this;
	}

	public function getPropertyNames()
	{
		$this->lazyLoadConfiguration();

		return $this->propertyNames;
	}

	public function getFields()
	{
		$this->lazyLoadConfiguration();

		return $this->fields;
	}

	public function setFields(array $items)
	{
		$this->getFields()->set($items);

		return $this;
	}

	public function getField($name)
	{
		return $this->getFields()->get($name);
	}

	public function getRels()
	{
		$this->lazyLoadConfiguration();

		return $this->rels;
	}

	public function setRels(array $rels)
	{
		$this->getRels()->set($rels);

		return $this;
	}

	public function getRel($name)
	{
		return $this->getRels()->get($name);
	}

	public function getValidators()
	{
		$this->lazyLoadConfiguration();

		return $this->validators;
	}

	public function setValidators(array $validators)
	{
		$this->lazyLoadConfiguration();

		$this->getValidators()->set($validators);

		return $this;
	}

	public function getEventListeners()
	{
		$this->lazyLoadConfiguration();

		return $this->eventListeners;
	}

	public function dipatchModelEvent($type, Model $target)
	{
		if ($this->getEventListeners()->has($type))
		{
			return $this->getEventListeners()->dispatchEvent(new ModelEvent($type, $target));
		}
		else
		{
			return TRUE;
		}
	}

	function __construct($class_name)
	{
		$this->modelClass = $class_name;
	}

	public function lazyLoadConfiguration()
	{
		if ($this->configurationLoaded === NULL)
		{
			$this->configurationLoaded = TRUE;

			$this->validators = new Validators();
			$this->fields = new Fields();
			$this->eventListeners = new EventListeners();
			$this->rels = new Rels();

			$class = new ReflectionClass($this->getModelClass());
			$this->table = $this->name = strtolower($class->getShortName());
			$this->propertyNames = Arr::invoke($class->getProperties(ReflectionProperty::IS_PUBLIC), 'getName');

			$this->callInitializeMethod($class);

			foreach ($class->getTraits() as $trait)
			{
				$this->callInitializeMethod($trait);
			}

			$this->rels->initialize($this);
		}
	}

	private function callInitializeMethod(ReflectionClass $class)
	{
		$name = str_replace('\\', '_', $class->getName());

		if ($class->hasMethod($name))
		{
			call_user_func($class->getName().'::'.$name, $this);
		}
	}
}
