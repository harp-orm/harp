<?php namespace CL\Luna\Event;

use CL\Luna\EntityManager\AbstractJob;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class JobEvent extends Event
{
	const EXECUTE = 1;
	const PROCESS = 2;

	public function __construct($type, AbstractJob $job)
	{
		parent::__construct($type, $target);
	}

}
