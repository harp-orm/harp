<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Collection;
use SplObjectStorage;

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

    public function updateRels()
    {
        foreach ($this->items as $item) {
            $item->getRel()->update($this->node, $item);
        }
    }

    public function deleteRels()
    {
        foreach ($this->items as $item) {
            if ($item->getRel() instanceof DeleteCascadeInterface) {
                $item->getRel()->delete($this->node, $item);
            }
        }
    }
}
