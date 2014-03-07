<?php namespace CL\Luna\Rel;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Model\LinkInterface;
use CL\Luna\Util\Arr;

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

	public function getJoinCondition($table, array $conditions)
	{
		$parts = [];

		foreach ($conditions as $foreignColumn => $column)
		{
			$parts []= "{$this->getName()}.{$foreignColumn} = {$table}.{$column}";
		}

		if ($this->getForeignSchema()->getSoftDelete())
		{
			$parts []= $this->getName().'.'.Schema::SOFT_DELETE_KEY.' IS NULL';
		}

		return 'ON '.join(' AND ', $parts);
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

	public function getForeignTable()
	{
		return $this->getForeignSchema()->getTable();
	}

	public function getTable()
	{
		return $this->getSchema()->getTable();
	}

	public function getPrimaryKey()
	{
		return $this->getSchema()->getPrimaryKey();
	}

	public function getForeignPrimaryKey()
	{
		return $this->getForeignSchema()->getPrimaryKey();
	}

	public function getSelectForModels(array $models)
	{
		$parentKeys = array_filter(array_unique(Arr::extract($models, $this->getKey())));

		if ($parentKeys)
		{
			return $this->getSelect()->where([$this->getForeignKey() => $parentKeys]);
		}
		else
		{
			return NULL;
		}
	}

	abstract public function initialize();
	abstract public function getKey();
	abstract public function getForeignKey();
	abstract public function getSelect();
	abstract public function setLinks(array $models, array $related);
	abstract public function update(Model $model, LinkInterface $related);



}
