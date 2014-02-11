<?php namespace CL\Luna\Event;

use CL\Luna\Model\Model;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelEvent extends Event
{
	const SAVE = 1;
	const AFTER_SAVE = 2;

	const DELETE = 3;
	const AFTER_DELETE = 4;

	const VALIDATE = 5;
	const AFTER_VALIDATE = 6;

	public function __construct($type, Model $target)
	{
		parent::__construct($type, $target);
	}

}
