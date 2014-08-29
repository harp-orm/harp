<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait LoadModelsTrait
{
    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasModels(Models $models)
    {
        return ! $models->isEmptyProperty($this->getKey());
    }

    /**
     * @param  Models $models
     * @param  integer $flags
     * @return \Harp\Harp\AbstractModel[]
     */
    public function loadModels(Models $models, $flags = null)
    {
        return $this->findModels($models, $flags)->loadRaw();
    }

    /**
     * @param  Models $models
     * @param  integer $flags
     * @return \Harp\Harp\Find
     */
    public function findModels(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this->findAllWhereIn($this->getForeignKey(), $keys, $flags);
    }
}
