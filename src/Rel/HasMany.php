<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\DB\UpdateSchema;
use CL\Luna\DB\SelectSchema;
use CL\Luna\Util\Arr;
use CL\Luna\Rel\Feature\SaveManyInterface;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractEagerLoaded implements SaveManyInterface
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

	public function load(Model $parent)
	{
		$query = (new SelectSchema($this->getForeignSchema()))
			->where([$this->getForeignKey() => $parent->getId()]);

		$items = $query->execute()->fetchAll();

		return new Many($items);
	}

	public function joinRel($query, $alias, $type)
	{
		$table = $alias ? [$this->getForeignSchema()->getTable() => $alias] : $this->getForeignSchema()->getTable();

		$query->join($table, [($alias ? $alias : $table).'.'.$this->getForeignKey() => $this->getSchema()->getTable().'.'.$this->getSchema()->getPrimaryKey()], $type);
	}

	public function getQuery($id)
	{
		return (new SelectSchema($this->getForeignSchema()))
			->where([$this->getForeignKey() => $id]);
	}

	public function scopeEagerLoaded(SelectSchema $select, array $parents)
	{
		$ids = array_filter(Arr::extract($parents, $this->getKey()));

		if ($ids)
		{
			$select->where([$this->getForeignKey() => $ids]);
			return TRUE;
		}
	}

	public function setEagerLoaded(array $parents, array $children)
	{
		$parents = Arr::index($parents, $this->getKey());
		$children = Arr::indexGroup($children, $this->getForeignKey());

		foreach ($parents as $id => $parent)
		{
			$items = isset($children[$id]) ? $children[$id] : [];
			$parent->setRel($this->getName(), new Many($items));
		}
	}

	public function saveMany(Model $parent, Many $many)
	{
		$changedIds = $many
			->getChanged()
				->setProperties([$this->getForeignKey() => $parent->getId()])
				->save()
				->getIds();

		$removeIds = array_diff($many->getOriginalIds(), $many->getIds());
		$addIds = array_diff($many->getIds(), $many->getOriginalIds(), $changedIds);

		$set = array_fill_keys($removeIds, [$this->getForeignKey() => NULL]) + array_fill_keys($addIds, [$this->getForeignKey() => $parent->getId()]);

		if ($set)
		{
			(new UpdateSchema($this->getForeignSchema()))
			->setMultiple($set)
			->execute();
		}
	}
}
