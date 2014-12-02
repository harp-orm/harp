<?php

namespace Harp\Harp\Graph;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Graph
{
    private $leafs;

    public function __construct()
    {
        $this->leafs = new SplObjectStorage();
    }

    public function getLeafOne(NodeInterface $parent, EdgeOneInterface $edge)
    {
        if (false === $this->leafs->contain($edge)) {
            $this->leafs[$edge] = $edge->getLeaf($parent);
        }

        $this->leafs[$edge];
    }

    public function getLeafMany(NodeInterface $parent, EdgeManyInterface $edge)
    {
        if (false === $this->leafs->contain($edge)) {
            $this->leafs[$edge] = $edge->getLeaf($parent);
        }

        $this->leafs[$edge];
    }

}
