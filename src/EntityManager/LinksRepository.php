<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Arr;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinksRepository
{
	private $links;
	private $em;

	function __construct(EntityManager $em)
	{
		$this->links = new SplObjectStorage();
		$this->em = $em;
	}

	public function all()
	{
		return $this->links;
	}

	public function add(Link $link)
	{
		if ( ! isset($this->links[$link->getParent()]))
		{
			$this->links[$link->getParent()] = new SplObjectStorage();
		}

		$this->links[$link->getParent()][$link->getRel()] = $link;

		return $this;
	}

	public function addArray(LinkArray $array)
	{
		foreach ($array->all() as $link)
		{
			$this->add($link);
		}

		return $this;
	}

	public function getForModel(Model $model)
	{
		if (isset($this->links[$model]))
		{
			return $this->links[$model];
		}
	}

	public function getForRel($model, $rel)
	{
		if (isset($this->links[$model]) AND isset($this->links[$model][$rel]))
		{
			return $this->links[$model][$rel];
		}
	}
}
