<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Model\LinkInterface;
use CL\Luna\Field\Integer;
use CL\Luna\Schema\Schema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRel
{
	protected $key;

	public function getKey()
	{
		return $this->key;
	}

	public function getForeignKey()
	{
		return $this->getSchema()->getPrimaryKey();
	}

	public function getSelect()
	{
		return $this->getForeignSchema()->getSelectSchema();
	}

	public function setLinks(array $models, array $related)
	{
		$related = Arr::index($related, $this->getForeignKey());

		foreach ($models as $model)
		{
			$index = $model->{$this->getKey()};

			$model->setLink(
				$this,
				isset($related[$index])
					? $related[$index]
					: $this->getForeignSchema()->getModelReflection()->newInstance(NULL, Model::NOT_LOADED)
			);
		}
	}

	public function initialize()
	{
		if ( ! $this->key)
		{
			$this->key = $this->getForeignSchema()->getName().'_id';
		}

		$this->getSchema()->getFields()->add(new Integer($this->key));
	}

	public function update(Model $model, LinkInterface $related)
	{
		$model->{$this->getForeignKey()} = $related->getId();
	}

	public function joinRel($query, $parent)
	{
		$table = $parent ?: $this->getTable();
		$columns = [$this->getForeignPrimaryKey() => $this->getForeignKey()];

		$query->join([$this->getForeignTable() => $this->getName()], $this->getJoinCondition($table, $columns));
	}
}