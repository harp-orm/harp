<?php namespace CL\Luna\Schema;

use CL\Luna\Event\Event;
use CL\Luna\Util\Collection;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EventListeners extends Collection {

    public function add($type, $listener)
    {
        $this->items[$type] []= $listener;

        return $this;
    }

    public function dispatchEvent($type, Model $target)
    {
        if (isset($this->items[$type]))
        {
            foreach ($this->items[$type] as $listner)
            {
                call_user_func($listner, $target);
            }
        }
    }
}
