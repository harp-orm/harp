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

    public function add($event, $listener)
    {
        $this->items[$event] []= $listener;

        return $this;
    }

    public function hasEvent($event)
    {
        return isset($this->items[$event]);
    }

    public function dispatchEvent($event, Model $target)
    {
        if ($this->hasEvent($event))
        {
            foreach ($this->items[$event] as $listner)
            {
                call_user_func($listner, $target);
            }
        }
    }
}
