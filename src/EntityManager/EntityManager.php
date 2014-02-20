<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Rel\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EntityManager
{
	private static $instance;

	public static function getInstance()
	{
		if (self::$instance === NULL)
		{
			self::$instance = new EntityManager();
		}
		return self::$instance;
	}

	private $jobs;
	private $saveJobs;
	private $items;

	public function add(Job $job)
	{
		$this->jobs []= $job;

		return $this;
	}

	public function getCanonicalItems(array $items)
	{
		foreach ($items as & $item)
		{
			$name = $item->getSchema()->getName();
			$id = $item->getId();

			if (isset($this->items[$name][$id]))
			{
				$item = $this->items[$name][$id];
			}
			else
			{
				$this->items[$name][$id] = $item;
			}
		}

		return $items;
	}

	public function load(RelContent $content)
	{
		$childJob = new ChildJob($content->getRel());
		$childJob->addRelContent($content);

		$this
			->add($childJob)
			->execute();
	}

	public function execute()
	{
		foreach ($this->jobs as $job)
		{
			$job->execute();
			$job->setResult($this->getCanonicalItems($job->getResult()));
			$job->processResult();
		}
		$this->jobs = NULL;

		return $this;
	}

	public function preserve(Model $model)
	{
		$queue = new Preserve();

		foreach (func_get_args() as $model)
		{
			$queue->addModel($model);
		}

		foreach ($queue->all() as $job)
		{
			$job->execute();
		}

		return $this;
	}
}
