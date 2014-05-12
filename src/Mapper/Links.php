<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Collection;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Links extends Collection
{
    protected $node;

    function __construct(AbstractNode $node)
    {
        $this->node = $node;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function add($name, AbstractLink $link)
    {
        $this->items[$name] = $link;

        return $this;
    }

    public function getNodes()
    {
        $all = new SplObjectStorage();

        foreach ($this->items as $item) {
            $all->addAll($item->getAll());
        }

        return $all;
    }

    public function eachRel(Closure $yield)
    {
        foreach ($this->items as $item) {
            $yield($item->getRel(), $this->node, $item);
        }
    }
}
