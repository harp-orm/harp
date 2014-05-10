<?php

namespace CL\Luna\Model;

use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EventListeners {

    public static function dispatchEvent($listeners, $event, Model $target)
    {
        if (isset($listeners[$event]))
        {
            foreach ($listeners[$event] as $listner)
            {
                call_user_func($listner, $target);
            }
        }
    }

    protected $before;
    protected $after;

    public function getBefore()
    {
        return $this->before;
    }

    public function getAfter()
    {
        return $this->after;
    }

    public function addBefore($event, $listener)
    {
        $this->before[$event] []= $listener;

        return $this;
    }

    public function addAfter($event, $listener)
    {
        $this->after[$event] []= $listener;

        return $this;
    }

    public function hasBeforeEvent($event)
    {
        return isset($this->before[$event]);
    }

    public function hasAfterEvent($event)
    {
        return isset($this->after[$event]);
    }

    public function dispatchAfterEvent($models, $event)
    {
        foreach ($models as $model) {
            self::dispatchEvent($this->after, $event, $model);
        }
    }

    public function dispatchBeforeEvent($models, $event)
    {
        foreach ($models as $model) {
            self::dispatchEvent($this->before, $event, $model);
        }
    }
}
