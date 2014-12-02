<?php

namespace Harp\Harp\Graph;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LeafOne
{
    private $parent;
    private $child;
    private $original;
    private $edge;

    public function __construct($edge, $parent, $child = null)
    {
        $this->edge = $edge;
        $this->parent = $parent;

        if ($child) {
            $this->original =$this->child = $child;
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function get()
    {
        if (null === $this->child) {
            $this->child = $this->original = $this->edge->load();
        }

        return $this->child;
    }

    public function set($node)
    {
        $this->child = $child;
    }

    public function getOriginal()
    {
        return $this->original;;
    }
}
