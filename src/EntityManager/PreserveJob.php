<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PreserveJob
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
	private $links;
	private $schema;

	function __construct(Model $model)
	{
		$this->type = self::getModelType($model);
		$this->schema = $model->getSchema();
		$this->addModel($model);
	}

	public function getSchema()
	{
		return $this->schema;
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

	public function addLink(Link $link)
	{
		$this->links []= $link;

		return $this;
	}

	public function updateLinks()
	{
		if ($this->links)
		{
			foreach ($this->links as $link)
			{
				$link->update();
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
		$this->getSchema()->getQuerySchema($this->type)->setModels($this->models)->execute();

		$this->preserveModels();

		$this->updateLinks();

		return $this;
	}
}
