<?php namespace CL\Luna\Schema\Query;

use CL\Atlas\Query\InsertQuery;
use CL\Luna\Schema\Schema;
use CL\Luna\Util\Log;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends InsertQuery implements SetModelsInterface {

	use QueryTrait;

	private $insertModels;

	public function __construct(Schema $schema)
	{
		$this
			->setSchema($schema)
			->into($schema->getTable());
	}

	public function setMultiple(array $values)
	{
		$columns = $this->schema->getFields()->getNames();

		$this->columns($columns);

		$defaultValues = $this->schema->getFields()->getDefaults();

		foreach ($values as $value)
		{
			$this->values(array_merge($defaultValues, $value));
		}

		return $this;
	}

	public function setModels(array $models)
	{
		$this->insertModels = $models;
		$changes = Arr::invoke($models, 'getChanges');
		$this->setMultiple($changes);

		return $this;
	}

	public function execute()
	{
		if (Log::getEnabled())
		{
			Log::add($this->humanize());
		}

		$result = parent::execute();

		if ($this->insertModels)
		{
			$lastInsertId = $this->db()->lastInsertId();

			foreach ($this->insertModels as $model)
			{
				$model->setInserted($lastInsertId);
				$lastInsertId += 1;
			}
			$this->insertModels = NULL;
		}

		return $result;
	}
}
