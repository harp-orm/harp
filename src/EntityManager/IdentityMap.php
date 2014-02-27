<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
	private $map;

	public function getModels(array $models)
	{
		foreach ($models as & $model)
		{
			$model = $this->getModel($model);
		}

		return $models;
	}

	public function getModel(Model $model)
	{
		$name = $model->getSchema()->getName();
		$id = $model->getId();

		if (isset($this->map[$name][$id]))
		{
			$model = $this->map[$name][$id];
		}
		else
		{
			$this->map[$name][$id] = $model;
		}

		return $model;
	}
}
