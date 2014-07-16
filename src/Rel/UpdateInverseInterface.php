<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;

/**
 * This interface allows rels to update their inverse counterparts,
 * e.g. Many rel after adding an item will set the corresponding belongsTo rel.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface UpdateInverseInterface
{
    public function updateInverse(AbstractModel $model, AbstractModel $foreign);
}
