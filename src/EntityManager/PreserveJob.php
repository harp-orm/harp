<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PreserveJob extends AbstractJob
{
	public static function getModelType(Model $model)
	{
		if ( ! $model->isLoaded())
		{
			return Schema::INSERT;
		}
		elseif ($model->isDeleted())
		{
			return Schema::DELETE;
		}
		elseif ($model->getId())
		{
			return Schema::UPDATE;
		}
	}

	private $models;
	private $type;
	private $relContents;

	function __construct(Model $model)
	{
		$this->type = self::getModelType($model);
		$this->addModel($model);

		parent::__construct($model->getSchema()->getQuerySchema($this->type));
	}

	public function getType()
	{
		return $this->type;
	}

	public function matches(Model $model)
	{
		return ($this->getSchema() === $model->getSchema() AND self::getModelType($model) === $this->getType());
	}

	public function addModel(Model $model)
	{
		$this->models []= $model;

		return $this;
	}

	public function addRelContent(RelContent $relContent)
	{
		$this->relContents []= $relContent;

		return $this;
	}

	public function updateRelContents()
	{
		if ($this->relContents)
		{
			foreach ($this->relContents as $relContent)
			{
				$relContent->update();
			}
		}
	}

	public function preserveModels()
	{
		foreach ($this->models as $model)
		{
			$model->preserve();
		}
	}

	public function execute()
	{
		$this->getQuery()->setModels($this->models);

		parent::execute();

		$this->preserveModels();

		$this->updateRelContents();

		return $this;
	}
}
