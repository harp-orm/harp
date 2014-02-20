<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Util\Arr;
use Closure;
/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ChildJob extends Job
{
	private $rel;
	private $relContents;

	function __construct(AbstractRel $rel)
	{
		parent::__construct($rel->getSelect());
		$this->rel = $rel;
	}

	public function getRel()
	{
		return $this->rel;
	}

	public function addRelContent(RelContent $relContent)
	{
		$this->relContents []= $relContent;

		return $this;
	}

	public function execute()
	{
		$ids = array_unique(Arr::invoke($this->relContents, 'getParentKey'));

		$this->getSelect()->where([$this->rel->getForeignKey() => $ids]);

		parent::execute();
	}

	public function getIndexedResult()
	{
		if ($this->rel instanceof SingleInterface)
		{
			return Arr::index($this->getResult(), $this->rel->getForeignKey());
		}
		elseif ($this->rel instanceof MultiInterface)
		{
			return Arr::indexGroup($this->getResult(), $this->rel->getForeignKey());
		}
	}

	public function processResult()
	{
		$results = $this->getIndexedResult();

		foreach ($this->relContents as $relContent)
		{
			$index = $relContent->getParent()->{$this->rel->getKey()};

			if ($this->rel instanceof MultiInterface)
			{
				$relContent->setContent(new ModelCollection(isset($results[$index]) ? $results[$index] : array()));
			}
			elseif ($this->rel instanceof SingleInterface)
			{
				$relContent->setContent(isset($results[$index]) ? $results[$index] : NULL);
			}
		}

		parent::processResult();
	}
}
