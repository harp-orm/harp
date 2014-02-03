<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractRel
{
	protected $foreignKey;

	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	public function initialize(Schema $schema, $name)
	{
		parent::initialize($schema, $name);

		if ( ! $this->foreignKey)
		{
			$this->foreignKey = $schema->getName().'_id';
		}
	}

	public function getQueryKey()
	{
		return $this->getForeignSchema()->getTable().'.'.$this->getForeignKey();
	}

	public function load(Model $parent)
	{
		$query = call_user_func([$this->getForeignClass(), 'all']);
		$query
			->where([$this->getQueryKey() => $parent->getId()]);

		$items = $query->execute()->fetchAll();

		return new Rels($items);
	}

	public function setRels(Model $parent, Rels $rels)
	{
		if ($parent->getId())
		{
			foreach ($rels->all() as $foreign)
			{
				$foreign->{$this->foreignKey} = $parent->getId();
			}
		}
	}

	public function saveRels(Model $parent, Rels $rels)
	{
		$oldIds = array_diff($rels->getOriginalIds(), $rels->getIds());
		if ($oldIds)
		{
			$query = call_user_func([$this->getForeignClass(), 'delete']);
			$query
				->where([$this->getForeignSchema()->getPrimaryKey() => $oldIds])
				->execute();
		}
	}
}
