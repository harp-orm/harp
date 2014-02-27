<?php namespace CL\Luna\EntityManager;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;
use CL\Luna\Rel\Feature\SingleInterface;
use CL\Luna\Rel\Feature\MultiInterface;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Arr;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkArray
{
	private $links;
	private $rel;
	private $content;

	function __construct(AbstractRel $rel, array $links = NULL)
	{
		$this->rel = $rel;
		$this->links = $links;
	}

	public function add(Link $link)
	{
		$this->links []= $link;
	}

	public function getRel()
	{
		return $this->rel;
	}

	public function all()
	{
		return $this->links;
	}

	public function getParentKeys()
	{
		return array_unique(Arr::invoke($this->links, 'getParentKey'));
	}

	public function getContentSelect()
	{
		$rel = $this->getRel();

		return $rel->getSelect()->where([$rel->getForeignKey() => $this->getParentKeys()]);
	}

	public function setContent(array $content)
	{
		$this->content = $content;

		if ($this->rel instanceof SingleInterface)
		{
			$this->setContentSingle($content);
		}
		elseif ($this->rel instanceof MultiInterface)
		{
			$this->setContentMulti($content);
		}
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContentSingle(array $content)
	{
		$models = Arr::index($content, $this->rel->getForeignKey());

		foreach ($this->links as $link)
		{
			$index = $link->getParentKey();
			$link->setContent(isset($models[$index]) ? $models[$index] : NULL);
		}
	}

	public function setContentMulti(array $content)
	{
		$models = Arr::indexGroup($content, $this->rel->getForeignKey());
		foreach ($this->links as $link)
		{
			$index = $link->getParentKey();
			$link->setContent(new ModelCollection(isset($models[$index]) ? $models[$index] : array()));
		}
	}
}
