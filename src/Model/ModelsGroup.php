<?php namespace CL\Luna\Model;

use CL\Luna\Util\ObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelsGroup extends ObjectStorage
{
	public function add(Model $model)
	{
		$this->attach($model);

		if ( ! $model->isEmptyLinks())
		{
			$this->addAll($model->getLinks()->getItems());
		}
	}

	public function getDeleted()
	{
		return $this->filter(function($model) {
			return $model->isDeleted();
		});
	}

	public function getPending()
	{
		return $this->filter(function($model) {
			return $model->isPending();
		});
	}

	public function getChanged()
	{
		return $this->filter(function($model) {
			return ($model->isChanged() AND ! $model->isDeleted());
		});
	}

	public function getSchemaStorage()
	{
		$schemaStorage = new ObjectStorage();

		foreach ($this as $item)
		{
			$schema = $item->getSchema();

			if (isset($schemaStorage[$schema]))
			{
				array_push($schemaStorage[$schema], $item);
			}
			else
			{
				$schemaStorage[$schema] = [$item];
			}
		}

		return $schemaStorage;
	}
}
