<?php namespace CL\Luna\Schema\Query;

use CL\Atlas\Query\DeleteQuery;
use CL\Luna\Schema\Schema;
use CL\Luna\Util\Log;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends DeleteQuery implements SetModelsInterface{

	use QueryTrait;

	public function __construct(Schema $schema)
	{
		$this
			->setSchema($schema)
			->from($schema->getTable());
	}

	public function execute()
	{
		if (Log::getEnabled())
		{
			Log::add($this->humanize());
		}

		return parent::execute();
	}

	public function setModels(array $models)
	{
		$ids = Arr::invoke($models, 'getId');
		$this->whereKey($ids);

		return $this;
	}

}
