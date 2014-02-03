<?php namespace CL\Luna\Rel;

use CL\Luna\Rel;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels
{
	protected $items;
	protected $originalIds;

	public function __construct(array $items)
	{
		$this->items = $items;
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
		return Arr::invoke($this->items, 'getId');
	}

	public function hasId($id)
	{
		foreach ($this->items as $item)
		{
			if ($item->getId() == $id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	public function getChanged()
	{
		return Arr::filterInvoke($this->items, 'isChanged');
	}

	public function add(Model $model)
	{
		if ( ! $this->has($model))
		{
			$this->items []= $model;
		}
	}

	public function set(array $new_items)
	{
		$this->items = $new_items;
	}
}
