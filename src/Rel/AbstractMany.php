<?php namespace CL\Luna\Rel;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\LinkMany;
use CL\Luna\Util\Arr;
use Closure;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractMany extends AbstractRel
{
    abstract function linkForeignKey(AbstractNode $model);
    abstract function linkKey(AbstractNode $model);

    public function loadForeignLinks(array $models, array $foreign, Closure $yield)
    {
        $foreign = Arr::groupBy($foreign, [$this, 'linkForeignKey']);

        foreach ($models as $model)
        {
            $index = $this->linkKey($model);

            $foreginModels = isset($foreign[$index]) ? $foreign[$index] : array();

            $yield($model, new LinkMany($this, $foreginModels));
        }
    }

}
