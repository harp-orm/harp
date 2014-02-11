<?php namespace CL\Luna\Event;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Event
{
	private $stopped;
	private $defaultPrevented;
	private $target;
	private $type;

	public function __construct($type, $target)
	{
		$this->type = $type;
		$this->target = $target;
	}

	public function preventDefault()
	{
		$this->defaultPrevented = TRUE;
		return $this;
	}

	public function stop()
	{
		$this->stopped = TRUE;
		return $this;
	}

	public function isStopped()
	{
		return $this->stopped;
	}

	public function isDefaultPrevented()
	{
		return $this->defaultPrevented;
	}

	public function getTarget()
	{
		return $this->target;
	}

	public function getType()
	{
		return $this->type;
	}
}
