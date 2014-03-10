<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelsGroup;
use CL\Luna\Schema\Query\Select;
use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Repo
{
	private static $instance;

	public static function getInstance()
	{
		if (self::$instance === NULL)
		{
			self::$instance = new Repo();
		}
		return self::$instance;
	}

	private $identityMap;

	public function __construct()
	{
		$this->identityMap = new IdentityMap();
	}

	public function getModel(Model $model)
	{
		$this->identityMap->getModel($model);
	}

	public function loadModels(Select $select)
	{
		$models = $select->execute()->fetchAll();
		return $this->identityMap->getModels($models);
	}

	public function loadLinks(Schema $schema, array $models, array $rels)
	{
		foreach ($rels as $relName => $childRelNames)
		{
			$rel = $schema->getRel($relName);

			$relatedModels = $this->loadLinkArray($rel, $models);

			if ($childRelNames)
			{
				$this->loadLinks($rel->getForeignSchema(), $relatedModels, $childRelNames);
			}
		}

		return $this;
	}

	public function loadLinkArray(AbstractRel $rel, array $models)
	{
		$select = $rel->getSelectForModels($models);

		$related = $select ? $this->loadModels($select) : array();

		$rel->setLinks($models, $related);

		return $related;
	}

	public function preserveArray(array $models)
	{
		array_walk($models, [$this, 'preserve']);

		return $this;
	}

	public function preserve(Model $model)
	{
		$models = new ModelsGroup();

		$models->add($model);

		$deleted = $models->getDeleted()->getSchemaStorage();

		foreach ($deleted as $schema)
		{
			$schema
				->getDeleteSchema()
					->setModels($deleted->getInfo())
					->execute();
		}

		$new = $models->getPending()->getSchemaStorage();

		foreach ($new as $schema)
		{
			$schema
				->getInsertSchema()
					->setModels($new->getInfo())
					->execute();

			foreach ($new->getInfo() as $model)
			{
				$model->updateLinks();
			}
		}

		$changed = $models->getChanged()->getSchemaStorage();

		foreach ($changed as $schema)
		{
			$schema
				->getUpdateSchema()
					->setModels($changed->getInfo())
					->execute();

			foreach ($changed->getInfo() as $model)
			{
				$model->updateLinks();
			}
		}

		return $this;
	}
}