<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo\LinkMany;

/**
 * This interface is used by relations that will delete foreign models
 * (e.g. when dissaciated with the parent model)
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface DeleteManyInterface
{
    /**
     * After deleting the models should return a collection of models, that have been deleted
     *
     * @param  LinkMany                     $link
     * @return \Harp\Harp\Model\Models|null
     */
    public function delete(LinkMany $link);
}
