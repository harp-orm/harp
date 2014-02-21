<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkLoader
{
	private $links;
	private $rel;
	private $linkedModels;

	function __construct(AbstractRel $rel)
	{
		$this->rel = $rel;
	}

	public function all()
	{
		return $this->links;
	}

	public function add(Link $link)
	{
		$this->links []= $link;
		return $this;
	}

	public function getParentKeys()
	{
		return array_unique(Arr::invoke($this->links, 'getParentKey'));
	}

	public function getSelect()
	{
		return $this->rel->getSelect()->where([$this->rel->getForeignKey() => $this->getParentKeys()]);
	}

	public function getLinkedModels()
	{
		return $this->linkedModels;
	}

	public function load()
	{
		$this->linkedModels = EntityManager::getInstance()->loadModels($this->getSelect());

		if ($this->rel instanceof SingleInterface)
		{
			$this->linkedModels = Arr::index($this->linkedModels, $this->rel->getForeignKey());
		}
		elseif ($this->rel instanceof MultiInterface)
		{
			$this->linkedModels = Arr::indexGroup($this->linkedModels, $this->rel->getForeignKey());
		}

		foreach ($this->links as $link)
		{
			$index = $link->getParentKey();

			if ($this->rel instanceof MultiInterface)
			{
				$link->setContent(new ModelCollection(isset($this->linkedModels[$index]) ? $this->linkedModels[$index] : array()));
			}
			elseif ($this->rel instanceof SingleInterface)
			{
				$link->setContent(isset($this->linkedModels[$index]) ? $this->linkedModels[$index] : NULL);
			}
		}
	}
}
