<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Field\Integer;
use CL\Luna\DB\SelectSchema;
use CL\Luna\Rel\Feature\SetOneInterface;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractEagerLoaded implements SetOneInterface
{
	protected $foreignKey;

	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	public function getKey()
	{
		return $this->getForeignSchema()->getPrimaryKey();
	}

	public function initialize()
	{
		if ( ! $this->foreignKey)
		{
			$this->foreignKey = $this->getForeignSchema()->getName().'_id';
		}

		$this->getSchema()->getFields()->add(new Integer($this->foreignKey));
	}

	public function load(Model $parent)
	{
		return (new SelectSchema($this->getForeignSchema()))
			->where([$this->getKey() => $parent->getId()])
			->limit(1)
			->execute()
				->fetch();
	}

	public function scopeEagerLoaded(SelectSchema $select, array $parents)
	{
		$ids = array_filter(Arr::extract($parents, $this->getForeignKey()));

		if ($ids)
		{
			$select->where([$this->getKey() => $ids]);
			return TRUE;
		}
	}

	public function setEagerLoaded(array $parents, array $children)
	{
		$parents = Arr::index($parents, $this->getKey());
		$children = Arr::index($children, $this->getForeignKey());

		foreach ($parents as $id => $parent)
		{
			$parent->setRel($this->getName(), isset($children[$id]) ? $children[$id] : NULL);
		}
	}

	public function setOne(Model $parent, Model $foreign)
	{
		$parent->{$this->getForeignKey()} = $foreign->getId();
	}

	public function joinRel($query, $alias, $type)
	{
		$table = $alias ? [$this->getForeignSchema()->getTable() => $alias] : $this->getForeignSchema()->getTable();
		$query->join($table, [($alias ? $alias : $table).'.'.$this->getSchema()->getPrimaryKey() => $this->getSchema()->getTable().'.'.$this->getForeignKey()], $type);
	}
}
