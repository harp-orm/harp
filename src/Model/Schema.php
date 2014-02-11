<?php namespace CL\Luna\Model;

use ReflectionClass;
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
	use EventDispatcherTrait;

	private $name;
	private $modelClass;
	private $table;
	private $db = 'default';
	private $primaryKey = 'id';
	private $fields;
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

	public function getFields()
	{
		$this->lazyLoadConfiguration();

		return $this->fields;
	}

	public function getField($name)
	{
		return $this->getField()[$name];
	}

	public function setFields(array $fields)
	{
		$this->fields = array_merge($this->fields, $fields);

		return $this;
	}

	public function getRels()
	{
		$this->lazyLoadConfiguration();

		return $this->rels;
	}

	public function getRel($name)
	{
		return $this->getRels()[$name];
	}

	public function setRels(array $rels)
	{
		$this->rels = array_merge($this->rels, $rels);

		return $this;
	}

	public function getValidators()
	{
		$this->lazyLoadConfiguration();

		return $this->validators;
	}

	public function setValidators(array $validators)
	{
		$this->validators = array_merge_recursive($this->validators, $validators);

		return $this;
	}

	private function dipatchModelEvent($type, Model $target)
	{
		if ($this->hasEventListener($type))
		{
			return $this->dispatchEvent(new ModelEvent($type, $target));
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

			$class = new ReflectionClass($this->getModelClass());
			$this->table = $this->name = strtolower($class->getShortName());

			$this->callInitializeMethod($class);

			foreach ($class->getTraits() as $trait)
			{
				$this->callInitializeMethod($trait);
			}

			foreach ($this->getRels() as $name => $rel)
			{
				$rel->initialize($this, $name);
			}
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
