<?php namespace CL\Luna\Event;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait EventDispatcherTrait
{
	private $listeners;

	public function addListener($event, $listener)
	{
		$this->listeners [$event] []= $listener;
	}

	public function hasEventListener($type)
	{
		return ($this->listeners AND isset($this->listenrs[$type]));
	}

	public function despatchEvent(Event $event)
	{
		foreach ($this->listenrs[$event->getType()] as $listner)
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
