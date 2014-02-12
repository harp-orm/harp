<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Field\Integer;
use CL\Luna\DB\SelectSchema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRel implements SetOneInterface
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
		return $this->getQuery($parent->getId())
			->limit(1)
			->execute()
				->fetch();
	}

	public function getQuery($id)
	{
		return (new SelectSchema($this->getForeignSchema()))
			->where([$this->getKey() => $id]);
	}

	public function getChildrenQuery(array $parents)
	{
		$ids = array_filter(Arr::extract($parents, $this->getForeignKey()));

		return $ids ? $this->getQuery($ids) : NULL;
	}

	public function setChildren(array $parents, array $children)
	{
		$parents = Arr::index($parents, $this->getForeignKey());

		$key = $this->getKey();
		$name = $this->getName();

		foreach ($children as $child)
		{
			$parents[$child->{$key}]->setRel($name, $child);
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
