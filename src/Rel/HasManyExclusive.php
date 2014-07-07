<?php

namespace Harp\Harp\Rel;

use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\DeleteManyInterface;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyExclusive extends HasMany implements DeleteManyInterface
{
    /**
     * @param  LinkMany      $link
     * @return Models
     */
    public function delete(LinkMany $link)
    {
        foreach ($link->getRemoved() as $removed) {
            $removed->delete();
        }

        return $link->getRemoved();
    }

    public function update(LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
        }
    }
}
