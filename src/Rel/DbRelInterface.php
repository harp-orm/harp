<?php

namespace Harp\Db\Rel;

use Harp\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DbRelInterface
{
    public function join(AbstractQuery $query, $parent);
}
