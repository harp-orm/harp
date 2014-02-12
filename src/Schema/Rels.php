<?php namespace CL\Luna\Schema;

use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Collection;
use CL\Luna\Model\Model;
use CL\Luna\Rel\SetOneInterface;
use CL\Luna\Rel\SetManyInterface;
use CL\Luna\Rel\SaveOneInterface;
use CL\Luna\Rel\SaveManyInterface;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels extends Collection {

	public function add(AbstractRel $item)
	{
		$this->items[$item->getName()] = $item;

		return $this;
	}

	public function initialize(Schema $schema)
	{
		if ($this->items)
		{
			foreach ($this->items as $item)
			{
				$item
					->setSchema($schema)
					->initialize();
			}
		}
	}

	public function setArray(Model $subject, array $objects)
	{
		foreach ($objects as $name => $object)
		{
			$item = $this->get($name);

			if ($item instanceof SetOneInterface)
			{
				$item->setOne($subject, $object);
			}
			elseif ($item instanceof SetManyInterface)
			{
				$item->setMany($subject, $object);
			}
		}
	}

	public function saveArray(Model $subject, array $objects)
	{
		foreach ($objects as $name => $object)
		{
			$item = $this->get($name);

			if ($item instanceof SaveOneInterface)
			{
				$item->saveOne($subject, $object);
			}
			elseif ($item instanceof SaveManyInterface)
			{
				$item->saveMany($subject, $object);
			}
		}
	}
}
