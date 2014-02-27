<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Rel\Link;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class WorkQueue
{
	private $units = [];

	public function all()
	{
		return $this->units;
	}

	public function add(UnitOfWork $unit, $type = AbstractRel::APPEND)
	{
		if ($type == AbstractRel::APPEND)
		{
			array_push($this->units, $unit);
		}
		elseif ($type = AbstractRel::PREPEND)
		{
			array_unshift($this->units, $unit);
		}

		return $this;
	}

	public function addModel(Model $model, $type = AbstractRel::APPEND)
	{
		if (($work = $this->findMatching($model)))
		{
			$work->addModel($model);
		}
		else
		{
			$work = new UnitOfWork($model);

			$this->add($work, $type);

			if (($relatedModels = $model->getRelated()))
			{
				foreach ($relatedModels as $relName => $related)
				{
					foreach ($related->getAffected() as $affected)
					{
						$this->addModel($affected, $model->getSchema()->getRel($relName)->getSavePriority());
					}
				}
			}
		}
	}

	public function findMatching(Model $model)
	{
		foreach ($this->units as $unit)
		{
			if ($unit->matches($model))
			{
				return $unit;
			}
		}
	}
}
