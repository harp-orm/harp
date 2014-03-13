<?php namespace CL\Luna\Model;

use CL\Luna\Util\Collection;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Errors extends Collection {

    public function add(Error $error)
    {
        $this->items[] = $error;
    }
}
