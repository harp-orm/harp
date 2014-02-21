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
		$this->links = new SplObjectStorage();
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

	public function setCanonicalLink(Model $model, AbstractRel $rel, Link $link)
	{
		if ( ! isset($this->links[$model]))
		{
			$this->links[$model] = new SplObjectStorage;
		}

		$this->links[$model][$rel] = $link;
	}

	public function getCanonicalLink($model, $rel)
	{
		return $this->link[$model][$rel];
	}

	public function loadLink($model, $rel)
	{
		if (isset($this->links[$model]) AND isset($this->links[$model][$rel]))
		{
			return $this->links[$model][$rel];
		}
		else
		{
			$link = new Link($model, $rel);
			$this->setCanonicalLink($model, $rel, $link);

			(new LinkLoader($rel))
				->add($link)
				->load();

			return $link;
		}
	}

	public function getLinks($model)
	{
		if (isset($this->links[$model]))
		{
			return $this->links[$model];
		}
	}

	public function loadLinks(Schema $schema, array $models, array $rels)
	{
		foreach ($rels as $relName => $childRelNames)
		{
			$rel = $schema->getRel($relName);
			$linkLoader = new LinkLoader($rel);

			foreach ($models as $model)
			{
				$link = new Link($model, $rel);
				$linkLoader->add($link);

				$this->setCanonicalLink($model, $rel, $link);
			}

			$linkLoader->load();

			if ($childRelNames)
			{
				$this->loadLinks($rel->getForeignSchema(), $linkLoader->getLinkedModels(), $childRelNames);
			}
		}

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
