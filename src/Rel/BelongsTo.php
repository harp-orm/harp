<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Field\Integer;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRel
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
			$name = call_user_func([$this->getForeignClass(), 'getName']);

			$this->foreignKey = $name.'_id';
		}

		$schema->setFields([
			$this->foreignKey => new Integer(),
		]);
	}

	public function load(Model $parent)
	{
		$query = call_user_func([$this->getForeignClass(), 'all']);
		$foreignTable = $this->getForeignSchema()->getTable();

		return $query
			->where([$foreignTable.'.'.$this->getForeignKey() => $parent->getId()])
			->limit(1)
			->execute()
				->fetch();
	}

	public function setRel(Model $parent, Model $foreign)
	{
		$parent->{$this->foreignKey} = $foreign->getId();
	}

	public function saveRel(Model $parent, Model $foreign)
	{
	}
}
