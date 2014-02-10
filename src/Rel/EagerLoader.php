<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Rel\AbstractRel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EagerLoader
{
	protected $parents;
	protected $children;
	protected $rel;

	public function __construct(array $parents, AbstractRel $rel)
	{
		$this->parents = $parents;
		$this->rel = $rel;
	}

	public function getRel()
	{
		return $this->rel;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function getParents()
	{
		return $this->parents;
	}

	public function getChildrenQuery()
	{
		return $this->rel->getChildrenQuery($this->getParents());
	}

	public function execute()
	{
		if (($query = $this->getChildrenQuery()))
		{
			$this->children = $this->getChildrenQuery()->execute()->fetchAll();
		}
		else
		{
			$this->children = NULL;
		}

		$this->rel->setChildren($this->parents, $this->children);

		return $this;
	}
}
