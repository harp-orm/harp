<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    public function newLink(array $foreign, IdentityMap $map)
    {
        $foreign = $map->getArray($foreign);

        return new LinkMany($this, $foreign);
    }

    public function newVoidLink()
    {
        return new LinkMany($this, array());
    }
}
