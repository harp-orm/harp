<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Model\Models;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface FindModelsInterface
{
    /**
     * @param  Models          $models
     * @param  integer         $flags
     * @return \Harp\Harp\Find
     */
    public function findModels(Models $models, $flags = null);
}
