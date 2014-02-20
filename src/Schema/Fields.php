<?php namespace CL\Luna\Schema;

use CL\Luna\Field\AbstractField;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Collection;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Fields extends Collection {

	public function add(AbstractField $item)
	{
		$this->items[$item->getName()] = $item;

		return $this;
	}

	public function getNames()
	{
		return array_keys($this->items);
	}

	public function getDefaults()
	{
		return Arr::invoke($this->items, 'getDefault');
	}

	public function loadData($data)
	{
		return Arr::invokeObjects($data, $this->items, 'load');
	}

	public function saveData($data)
	{
		return Arr::invokeObjects($data, $this->items, 'save');
	}

}
