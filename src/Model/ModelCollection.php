<?php namespace CL\Luna\Model;

use CL\Luna\Util\ObjectStorage;
use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelCollection extends ObjectStorage implements LinkInterface
{
	protected $items;
	protected $original;

	public function __construct(array $items)
	{
		$this->attachArray($items);

		$this->original = clone $this;
	}

	public function massAssignAll(Schema $schema, array $items)
	{
		$this->removeAll($this);

		foreach ($items as $properties)
		{
			$item = $schema->getModelReflection()->newInstance();
			Repo::getInstance()->getModel($item->massAssign($properties));
			$this->attach($item);
		}

		return $this;
	}

	public function getOriginal()
	{
		return $this->original;
	}

	public function getOriginalIds()
	{
		return $this->original->invoke('getId');
	}

	public function getIds()
	{
		return $this->invoke('getId');
	}

	public function getAdded()
	{
		$current = clone $this;
		$current->removeAll($this->original);
		return $current;
	}

	public function getRemoved()
	{
		$current = clone $this->original;
		$current->removeAll($this);
		return $current;
	}

	public function getAll()
	{
		$current = clone $this;
		$current->addAll($this->original);
		return $current;
	}
}
