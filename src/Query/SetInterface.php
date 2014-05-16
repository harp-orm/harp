<?php

namespace CL\Luna\Query;

use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface SetInterface
{
    public function setModels(SplObjectStorage $models);
}
