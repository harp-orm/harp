<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Preserve
{
	private $items = [];

	public function all()
	{
		return $this->items;
	}

	public function add(PreserveJob $job, $type = AbstractRel::APPEND)
	{
		if ($type == AbstractRel::APPEND)
		{
			array_push($this->items, $job);
		}
		elseif ($type = AbstractRel::PREPEND)
		{
			array_unshift($this->items, $job);
		}

		return $this;
	}

	public function addModel(Model $model, $type = AbstractRel::APPEND)
	{
		if (($job = $this->findMatching($model)))
		{
			$job->addModel($model);
		}
		else
		{
			$job = new PreserveJob($model);

			$this->add($job, $type);

			if (($linkRels = EntityManager::getInstance()->getLinks($model)))
			{
				foreach ($linkRels as $rel)
				{
					$link = $linkRels[$rel];

					foreach ($link->getAffected() as $model)
					{
						$this->addModel($model, $rel->getSavePriority());
					}

					$job->addLink($link);
				}
			}
		}
	}

	public function findMatching(Model $model)
	{
		foreach ($this->items as $item)
		{
			if ($item->matches($model))
			{
				return $item;
			}
		}
	}
}
