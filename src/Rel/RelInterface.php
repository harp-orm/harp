<?php

namespace Harp\Harp\Rel;

use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface RelInterface
{
    /**
     * @return void
     */
    public function join(AbstractWhere $query, $parent);
}
