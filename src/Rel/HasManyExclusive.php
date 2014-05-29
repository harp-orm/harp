<?php

namespace Harp\Db\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\DeleteManyInterface;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyExclusive extends HasMany implements DeleteManyInterface
{
    /**
     * @param  AbstractModel $model
     * @param  LinkMany      $link
     * @return Models
     */
    public function delete(AbstractModel $model, LinkMany $link)
    {
        foreach ($link->getRemoved() as $removed) {
            $removed->delete();
        }

        return $link->getRemoved();
    }

    public function update(AbstractModel $model, LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
