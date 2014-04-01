<?php namespace CL\Luna\Rel;

use CL\Luna\Repo\LinkOne;
use CL\Luna\Model\Model;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface LinkOneInterface
{
    public function update(Model $model, LinkOne $link);
}
