<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Objects;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    abstract function areLinked(AbstractNode $model, AbstractNode $foreign);

    public function newLink(AbstractNode $node)
    {
        $node = $node->getRepo()->getIdentityMap()->get($node);

        return new LinkOne($this, $node);
    }

    public function newEmptyLink()
    {
        return new LinkOne($this, $this->getForeignRepo()->newVoidInstance());
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function($model, $foreign) {
            return $this->areLinked($model, $foreign);
        });
    }
}
