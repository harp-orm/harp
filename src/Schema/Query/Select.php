<?php namespace CL\Luna\Schema\Query;

use CL\Luna\Schema\Schema;
use CL\Luna\EntityManager\EntityManager;
use CL\Luna\EntityManager\Job;
use CL\Luna\EntityManager\ChildJob;
use CL\Luna\EntityManager\RelLink;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;
use CL\Atlas\Query\SelectQuery;
use PDO;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends SelectQuery {

	use QueryTrait;

	public function __construct(Schema $schema)
	{
		$this
			->setSchema($schema)
			->from($schema->getTable())
			->columns($schema->getTable().'.*');
	}

	public function loadWith($rels)
	{
		$em = EntityManager::getInstance();
		$job = new Job($this);

		$em->add($job);

		$rels = Arr::toAssoc( (array) $rels);

		self::addLoadJobs($em, $job, $rels);

		$em->execute();

		return $job->getResult();
	}

	public static function addLoadJobs($em, $job, $rels)
	{
		$schema = $job->getSchema();

		foreach ($rels as $relName => $childRels)
		{
			$rel = $schema->getRel($relName);

			$childJob = new ChildJob($rel);
			$job->addChild($childJob);


			$em->add($childJob);

			if ($childRels)
			{
				self::addLoadJobs($em, $childJob, $childRels);
			}
		}
	}

	public function load()
	{
		$em = EntityManager::getInstance();
		$job = new Job($this);

		$em->add($job);
		$em->execute();

		return $job->getResult();
	}

	public function execute()
	{
		if (Log::getEnabled())
		{
			Log::add($this->humanize());
		}

		$pdoStatement = parent::execute();

		$pdoStatement->setFetchMode(PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), [NULL, TRUE]);

		return $pdoStatement;
	}
}
