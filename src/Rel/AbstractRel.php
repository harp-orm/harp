<?php namespace CL\Luna\Rel;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel
{
	protected $foreignSchema;
	protected $schema;
	protected $name;

	public function __construct($name, Schema $foreign_schema, array $properties = NULL)
	{
		$this->foreignSchema = $foreign_schema;
		$this->name = $name;

		if ($properties)
		{
			foreach ($properties as $propertyName => $value)
			{
				$this->$propertyName = $value;
			}
		}
	}

	public function setSchema(Schema $schema)
	{
		$this->schema = $schema;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSchema()
	{
		return $this->schema;
	}

	public function getForeignSchema()
	{
		return $this->foreignSchema;
	}

	abstract public function initialize();
}
