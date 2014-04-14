<?php namespace CL\Luna\Rel;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Util\Arr;
use Closure;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractOne extends AbstractRel
{
    abstract function linkForeignKey(AbstractNode $model);
    abstract function linkKey(AbstractNode $model);

    public function newForeignNotLoaded()
    {
        return $this->getForeignSchema()->newInstance(null, AbstractNode::NOT_LOADED);
    }

    public function loadForeignLinks(array $models, array $foreign, Closure $yield)
    {
        $foreign = Arr::index($foreign, [$this, 'linkForeignKey']);

        foreach ($models as $model)
        {
            $index = $this->linkKey($model);

            $foreginModel = isset($foreign[$index])
                ? $foreign[$index]
                : $this->newForeignNotLoaded();

            $yield($model, new LinkOne($this, $foreginModel));
        }

        return $foreign;
    }
}
