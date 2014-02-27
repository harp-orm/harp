<?php namespace CL\Luna\EntityManager;

use CL\Luna\Rel\AbstractRel;
use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Link
{
	public static function extractParentKeys(array $links)
	{
		return array_unique(Arr::invoke($links, 'getParentKey'));
	}

	public static function newFromModels(AbstractRel $rel, array $models)
	{
		return array_map(function($model) use ($rel) {
			return new Link($model, $rel);
		}, $models);
	}

	private $rel;
	private $content;
	private $parent;

	function __construct(Model $parent, AbstractRel $rel, $content = NULL)
	{
		$this->parent = $parent;
		$this->rel = $rel;
		$this->content = $content;
	}

	public function getRel()
	{
		return $this->rel;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getParentKey()
	{
		return $this->parent->{$this->rel->getKey()};
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setParent(Model $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function update()
	{
		$this->rel->update($this->parent, $this->content);
	}

	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	public function getAffected()
	{
		if ($this->content instanceof Model)
		{
			return [$content];
		}
		elseif ($this->content instanceof ModelCollection)
		{
			return $this->content->getAffected();
		}
	}
}
