<?php

namespace CL\Luna\Model;

use CL\Luna\Mapper\AbstractRel;
use CL\Luna\Util\Collection;
use CL\Luna\Util\Arr;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels extends Collection {

    public function add(AbstractRel $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    public function filterOnDelete()
    {
        if ($this->items) {
            return Arr::filterInvoke($this->items, 'getOnDelete');
        } else {
            return $items;
        }
    }
}
