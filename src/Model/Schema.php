<?php namespace CL\Luna\Model;

use ReflectionClass;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Schema
{
	private $name;
	private $table;
	private $db = 'default';
	private $primaryKey = 'id';
	private $fields = array();
	private $events = array();
	private $rels = array();
	private $validators = array();
	private $finalized = FALSE;

	public function getName()
	{
		return $this->name;
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;

		return $this;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function setTable($table)
	{
		$this->table = (string) $table;

		return $this;
	}

	public function getDb()
	{
		return $this->db;
	}

	public function setDb($db)
	{
		$this->db = (string) $db;

		return $this;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setFields(array $fields)
	{
		$this->fields = array_merge($this->fields, $fields);

		return $this;
	}

	public function getRels()
	{
		return $this->rels;
	}

	public function setRels(array $rels)
	{
		$this->rels = array_merge($this->rels, $rels);

		return $this;
	}

	public function getValidators()
	{
		return $this->validators;
	}

	public function setValidators(array $validators)
	{
		$this->validators = array_merge_recursive($this->validators, $validators);

		return $this;
	}

	function __construct($class_name)
	{
		$class = new ReflectionClass($class_name);
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

		$this->finialized = TRUE;
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
