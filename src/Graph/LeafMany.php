<?php

namespace Harp\Harp\Graph;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LeafMany
{
    private $parent;
    private $children;
    private $original;
    private $edge;

    public function __construct($edge, $parent, SplObjectStorage $children = null)
    {
        $this->edge = $edge;
        $this->parent = $parent;

        if ($children) {
            $this->original = $this->children = $children;
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function get()
    {
        if (null === $this->children) {
            $this->children = $this->original = $this->edge->load();
        }

        return $this->children;
    }

    public function add($node)
    {
        $this->get()->attach($node);
    }

    public function has($node)
    {
        $this->get()->contains($node);
    }

    public function remove($node)
    {
        $this->get()->remove($node);
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function isChanged()
    {
        return $this->original === $this->children;
    }
}
