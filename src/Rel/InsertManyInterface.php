<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo\LinkMany;

/**
 * This interface is used by relations that will add new foreign models
 * (e.g. when there is a link "through" model)
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface InsertManyInterface
{
    /**
     * Perform logic to preserve the link for newly inserted models.
     * Return a collection of new models
     *
     * @param  LinkMany                     $link
     * @return \Harp\Harp\Model\Models|null
     */
    public function insert(LinkMany $link);
}
