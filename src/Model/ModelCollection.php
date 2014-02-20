<?php namespace CL\Luna\Model;

use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelCollection
{
	protected $items;
	protected $original;
	protected $compare;

	public function __construct(array $items)
	{
		$this->set($items);
		$this->original = $items;
		$this->compare = function($a, $b) {
			return $a === $b ? 0 : 1;
		};
	}

	public function all()
	{
		return $this->items;
	}

	public function getOriginal()
	{
		return $this->original;
	}

	public function has(Model $model)
	{
		return $this->hasId($model->getId());
	}

	public function getOriginalIds()
	{
		return Arr::invoke($this->original, 'getId');
	}

	public function getIds()
	{
		return Arr::invoke($this->items, 'getId');
	}

	public function getAdded()
	{
		return array_udiff($this->items, $this->original, $this->compare);
	}

	public function getRemoved()
	{
		return array_udiff($this->original, $this->items, $this->compare);
	}

	public function getAffected()
	{
		return array_merge($this->original, $this->items);
	}

	public function getChanged()
	{
		return Arr::filterInvoke($this->items, 'isChanged');
	}

	public function save()
	{
		foreach ($this->getAffected() as $item)
		{
			$item->save();
		}
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

		return $this;
	}

	public function set(array $new_items)
	{
		$this->items = NULL;

		array_map([$this, 'add'], $new_items);

		return $this;
	}
}
