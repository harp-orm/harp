<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Schema;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel
{
	protected $foreignClass;
	protected $foreignSchema;
	protected $schema;
	protected $name;

	public function __construct($foreign_class, array $attributes = NULL)
	{
		$this->foreignClass = $foreign_class;

		if ($attributes)
		{
			foreach ($attributes as $name => $value)
			{
				$this->$name = $value;
			}
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSchema()
	{
		return $this->schema;
	}

	public function getForeignClass()
	{
		return $this->foreignClass;
	}

	public function getForeignQuery()
	{
		return call_user_func([$this->getForeignClass(), 'all']);
	}

	public function getForeignSchema()
	{
		if ($this->foreignSchema === NULL)
		{
			$this->foreignSchema = call_user_func([$this->foreignClass, 'getSchema']);
		}

		return $this->foreignSchema;
	}

	function initialize(Schema $schema, $name)
	{
		$this->schema = $schema;
		$this->name = $name;
	}

	abstract function load(Model $parent);

	// abstract function save(Model $parent);

}
