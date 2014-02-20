<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Util\Arr;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Schema\Query\Select;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractRel implements MultiInterface
{
	protected $foreignKey;

	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	public function getKey()
	{
		return $this->getSchema()->getPrimaryKey();
	}

	public function initialize()
	{
		if ( ! $this->foreignKey)
		{
			$this->foreignKey = $this->getSchema()->getName().'_id';
		}
	}

	public function getSelect()
	{
		return $this->getForeignSchema()->getSelectSchema();
	}

	public function joinRel($query, $parent)
	{
		$table = $parent ?: $this->getTable();
		$columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

		$query->join([$this->getForeignTable() => $this->getName()], $this->getJoinCondition($table, $columns));
	}

	public function update(Model $parent, ModelCollection $foreign)
	{
		foreach ($foreign->getAdded() as $item)
		{
			$item->{$this->getForeignKey()} = $parent->{$this->getKey()};
		}

		foreach ($foreign->getRemoved() as $item)
		{
			$item->{$this->getForeignKey()} = NULL;
		}
	}
}
