<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Rel\Link;
use CL\Luna\Schema\Query\Select;
use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Arr;
use SplObjectStorage;

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

	private $identityMap;

	public function __construct()
	{
		$this->identityMap = new IdentityMap();
	}

	public function loadModels(Select $select)
	{
		$models = $select->execute()->fetchAll();
		return $this->identityMap->getModels($models);
	}

	public function loadLink(Link $link)
	{
		$linkArray = new LinkArray($link->getRel(), [$link]);

		$this->loadLinkArray($linkArray);

		return $this;
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

		$rel->setRelated($models, $related);

		return $related;
	}

	public function preserve(Model $model)
	{
		$queue = new WorkQueue();

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
