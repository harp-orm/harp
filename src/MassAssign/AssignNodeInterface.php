<?php

namespace CL\Luna\MassAssign;

use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface AssignNodeInterface
{
    public function setData(array $data, Closure $yield);
}
