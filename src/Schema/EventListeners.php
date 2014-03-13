<?php namespace CL\Luna\Schema;

use CL\Luna\Event\Event;
use CL\Luna\Util\Collection;

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

    public function dispatchEvent(Event $event)
    {
        foreach ($this->items[$event->getType()] as $listner)
        {
            call_user_func($listner, $event->getTarget(), $event);

            if ($event->isStopped())
            {
                break;
            }
        }
        return ! $event->isDefaultPrevented();
    }
}
