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
	protected $foreignSchema;
	protected $schema;
	protected $name;

	public function __construct(Schema $foreign_schema, array $attributes = NULL)
	{
		$this->foreignSchema = $foreign_schema;

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

	public function getForeignSchema()
	{
		return $this->foreignSchema;
	}

	function initialize(Schema $schema, $name)
	{
		$this->schema = $schema;
		$this->name = $name;
	}
}
