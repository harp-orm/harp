<?php

namespace CL\Luna\Rel;

use CL\Util\Objects;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkMany;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyExclusive extends HasMany
{
    public function delete(AbstractModel $model, LinkMany $link)
    {
        Objects::invoke($link->getRemoved(), 'delete');

        return $link->getRemoved();
    }

    public function update(AbstractModel $model, LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
