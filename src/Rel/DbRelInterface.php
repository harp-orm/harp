<?php

namespace Harp\Db\Rel;

use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DbRelInterface
{
    /**
     * @return void
     */
    public function join(AbstractWhere $query, $parent);
}
