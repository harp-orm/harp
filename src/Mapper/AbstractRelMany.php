<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Objects;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    abstract function areLinked(AbstractNode $model, AbstractNode $foreign);

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

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::groupCombineArrays($models, $foreign, function($model, $foreign) {
            return $this->areLinked($model, $foreign);
        });
    }
}
