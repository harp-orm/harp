<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Schema\Query\Select;
use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
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

	private $jobs;
	private $items;
	private $links;

	public function __construct()
	{
		$this->links = new LinksRepository($this);
	}

	public function loadModels(Select $select)
	{
		$models = $select->execute()->fetchAll();

		$schema = $select->getSchema();
		$name = $schema->getName();
		$primaryKey = $schema->getPrimaryKey();

		foreach ($models as & $model)
		{
			$id = $model->{$primaryKey};

			$model = $this->getCanonicalModel($name, $id, $model);
		}

		return $models;
	}

	public function getCanonicalModel($name, $id, $model)
	{
		if (isset($this->items[$name][$id]))
		{
			return $this->items[$name][$id];
		}
		else
		{
			$this->items[$name][$id] = $model;
			return $model;
		}
	}

	public function loadLink($model, $rel)
	{
		if ($link = $this->links->getForRel($model, $rel))
		{
			return $link;
		}
		else
		{
			$link = new Link($model, $rel);
			$linkArray = new LinkArray($rel, [$link]);

			$this->loadLinkArray($linkArray);

			return $link;
		}
	}

	public function getLinks($model)
	{
		return $this->links->getForModel($model);
	}

	public function loadLinks(Schema $schema, array $models, array $rels)
	{
		foreach ($rels as $relName => $childRelNames)
		{
			$rel = $schema->getRel($relName);

			$linkArray = new LinkArray($rel);

			foreach ($models as $model)
			{
				$linkArray->add(new Link($model, $rel));
			}

			$this->loadLinkArray($linkArray);

			if ($childRelNames)
			{
				$this->loadLinks($rel->getForeignSchema(), $linkArray->getContent(), $childRelNames);
			}
		}

		return $this;
	}

	public function loadLinkArray(LinkArray $array)
	{
		$select = $array->getContentSelect();

		$linkedModels = $this->loadModels($select);

		$array->setContent($linkedModels);

		$this->links->addArray($array);

		return $this;
	}

	public function preserve(Model $model)
	{
		$queue = new Preserve();

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
