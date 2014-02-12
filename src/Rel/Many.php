<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Many
{
	protected $items;
	protected $originalIds;

	public function __construct(array $items)
	{
		$this->set($items);
		$this->originalIds = Arr::invoke($items, 'getId');
	}

	public function all()
	{
		return $this->items;
	}

	public function has(Model $model)
	{
		return $this->hasId($model->getId());
	}

	public function getOriginalIds()
	{
		return $this->originalIds;
	}

	public function getIds()
	{
		return $this->items ? Arr::invoke($this->items, 'getId') : [];
	}

	public function search(Model $model)
	{
		return $this->searchId($model->getId());
	}

	public function searchId($id)
	{
		if ( ! $this->items)
		{
			return FALSE;
		}

		foreach ($this->items as $index => $item)
		{
			if ($item->getId() == $id)
			{
				return $index;
			}
		}

		return FALSE;
	}

	public function hasId($id)
	{
		return $this->searchId() !== FALSE;
	}

	public function getChanged()
	{
		$changedItems = Arr::filterInvoke($this->items, 'isChanged');

		return new Many($changedItems);
	}

	public function setProperties($properties)
	{
		if ($this->items)
		{
			foreach ($this->items as $item)
			{
				$item->setProperties($properties);
			}
		}
		return $this;
	}

	public function save()
	{
		if ($this->items)
		{
			Arr::invoke($this->items, 'save');
		}

		return $this;
	}

	public function isEmpty()
	{
		return empty($this->items);
	}

	public function add(Model $model)
	{
		if (($index = $this->search($model)) !== FALSE)
		{
			$this->items[$index] = $model;
		}
		else
		{
			$this->items []= $model;
		}
	}

	public function set(array $new_items)
	{
		$this->items = NULL;

		array_map([$this, 'add'], $new_items);

		return $this;
	}
}
