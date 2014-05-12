<?php

namespace CL\Luna\Mapper;

use SplObjectStorage;
use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMap
{
    private $map;

    function __construct()
    {
        $this->map = new SplObjectStorage();
    }

    public function get(AbstractNode $node)
    {
        if ($this->map->contains($node)) {
            return $this->map[$node];
        } else {
            return $this->map[$node] = new Links($node);
        }
    }

    public function isEmpty(AbstractNode $node)
    {
        return (! $this->map->contains($node) or $this->map[$node]->isEmpty());
    }

    public function has(AbstractNode $node)
    {
        return $this->map->contains($node);
    }
}
