<?php namespace CL\Luna\ModelQuery;

use CL\Atlas\Query\AbstractQuery;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface RelJoinInterface
{
    public function joinRel(AbstractQuery $query, $parent);
}
