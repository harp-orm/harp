<?php namespace CL\Luna\Rel;

use CL\Luna\Repo\LinkMany;
use CL\Luna\Model\Model;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface LinkManyInterface
{
    public function update(Model $model, LinkMany $link);

    public function setLinks(array $models, array $related, Closure $set_link);
}
