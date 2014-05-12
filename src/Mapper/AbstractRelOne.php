<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    public function newLink(AbstractNode $node)
    {
        $node = $node->getRepo()->getIdentityMap()->get($node);

        return new LinkOne($this, $node);
    }

    public function newEmptyLink()
    {
        return new LinkOne($this, $this->getForeignRepo()->newVoidInstance());
    }
}
