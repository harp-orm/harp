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
		$models = $this->load();

		$rels = Arr::toAssoc( (array) $rels);

		EntityManager::getInstance()->loadLinks($this->getSchema(), $models, $rels);

		return $models;
	}

	public function load()
	{
		return EntityManager::getInstance()->loadModels($this);
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
