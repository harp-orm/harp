<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    public function newForeignVoid()
    {
        return $this->getForeignStore()->newInstance(null, AbstractNode::VOID);
    }

    public function newLink(AbstractNode $foreign, IdentityMap $map)
    {
        $foreign = $map->get($foreign);

        return new LinkOne($this, $foreign);
    }

    public function newVoidLink()
    {
        return new LinkOne($this, $this->newForeignVoid());
    }
}
