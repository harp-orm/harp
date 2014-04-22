<?php namespace CL\Luna\Mapper;

use SplObjectStorage;

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
        return $this->map->contains($model);
    }

    public function update(AbstractNode $node)
    {
        $links = $this->get($node);

        foreach ($links as $model) {
            $links->getInfo()->update();
        }
    }

    public function updateNodes(SplObjectStorage $nodes)
    {
        $nodes = clone $this->map;
        $nodes->removeAllExcept($nodes);

        foreach ($nodes as $node) {
            $nodes->getInfo()->update();
        }
    }

    public function addAllRecursive(SplObjectStorage $all, AbstractNode $node)
    {
        $all->attach($node);

        if (! $this->isEmpty($node)) {

            $linkedNodes = $this->get($node)->getNodes();
            foreach ($linkedNodes as $node) {
                $this->addAllRecursive($all, $node);
            }
        }

        return $all;
    }
}
