<?php namespace CL\Luna\Model;

use ReflectionClass;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Schema
{
	private $table;
	private $db = 'default';
	private $fields = array();
	private $events = array();
	private $rels = array();
	private $validators = array();
	private $finalized = FALSE;

	public function getTable()
	{
		return $this->table;
	}

	public function setTable($table)
	{
		$this->table = (string) $table;
	}

	public function getDb()
	{
		return $this->db;
	}

	public function setDb($db)
	{
		$this->db = (string) $db;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setFields(array $fields)
	{
		$this->fields = array_merge($this->fields, $fields);
	}

	public function getRels()
	{
		return $this->rels;
	}

	public function setRels(array $rels)
	{
		$this->rels = array_merge($this->rels, $rels);
	}

	public function getValidators()
	{
		return $this->validators;
	}

	public function setValidators(array $validators)
	{
		$this->validators = array_merge_recursive($this->validators, $validators);
	}

	function __construct($class_name)
	{
		$class = new ReflectionClass($class_name);
		$this->table = strtolower($class->getShortName());

		$this->callInitializeMethod($class);

		foreach ($class->getTraits() as $trait)
		{
			$this->callInitializeMethod($trait);
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
