<?php

namespace Harp\Harp\Repo;

use Harp\Harp\AbstractModel;

/**
 * Events in the lifecycle of models. Before and After events are separate.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class EventListeners
{
    /**
     * @param array         $listeners
     * @param AbstractModel $target
     * @param int           $event
     */
    public static function dispatchEvent($listeners, AbstractModel $target, $event)
    {
        if (isset($listeners[$event])) {
            foreach ($listeners[$event] as $listner) {
                call_user_func($listner, $target);
            }
        }
    }

    /**
     * @var array
     */
    private $before = [];

    /**
     * @var array
     */
    private $after = [];

    /**
     * @return array
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return array
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param int                  $event
     * @param string|array|Closure $listener
     */
    public function addBefore($event, $listener)
    {
        $this->before[$event] []= $listener;

        return $this;
    }

    /**
     * @param int                  $event
     * @param string|array|Closure $listener
     */
    public function addAfter($event, $listener)
    {
        $this->after[$event] []= $listener;

        return $this;
    }

    /**
     * @param  int     $event
     * @return boolean
     */
    public function hasBeforeEvent($event)
    {
        return isset($this->before[$event]);
    }

    /**
     * @param  int     $event
     * @return boolean
     */
    public function hasAfterEvent($event)
    {
        return isset($this->after[$event]);
    }

    /**
     * @param AbstractModel $target
     * @param int           $event
     */
    public function dispatchAfterEvent(AbstractModel $target, $event)
    {
         self::dispatchEvent($this->after, $target, $event);
    }

    /**
     * @param AbstractModel $target
     * @param int           $event
     */
    public function dispatchBeforeEvent(AbstractModel $target, $event)
    {
        self::dispatchEvent($this->before, $target, $event);
    }
}
