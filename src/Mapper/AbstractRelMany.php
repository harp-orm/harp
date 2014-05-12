<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    public function newLink(array $foreignNodes)
    {
        foreach ($foreignNodes as & $node) {
            $node = $node->getRepo()->getIdentityMap()->get($node);
        }

        return new LinkMany($this, $foreignNodes);
    }

    public function newEmptyLink()
    {
        return new LinkMany($this, array());
    }
}
