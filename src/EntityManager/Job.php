<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use Closure;
/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Job
{
	private $children;
	private $result;
	private $select;

	function __construct($select)
	{
		$this->select = $select;
	}

	public function execute()
	{
		$select = $this->getSelect();
		$result = $this->select->execute()->fetchAll();
		$this->setResult($result);
	}

	public function processResult()
	{
		if ($this->children)
		{
			foreach ($this->children as $child)
			{
				foreach ($this->getResult() as $result)
				{
					$relContent = new RelContent($child->getRel(), $result);
					$result->addRelContent($relContent);
					$child->addRelContent($relContent);
				}
			}
		}
	}

	public function getSelect()
	{
		return $this->select;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function setResult(array $result)
	{
		$this->result = $result;

		return $this;
	}

	public function getSchema()
	{
		return $this->select->getSchema();
	}

	public function addChild(ChildJob $child)
	{
		$this->children []= $child;
		return $this;
	}
}
