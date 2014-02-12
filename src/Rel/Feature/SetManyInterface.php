<?php namespace CL\Luna\Rel\Feature;

use CL\Luna\Model\Model;
use CL\Luna\Rel\Many;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface SetManyInterface
{
	public function setMany(Model $subject, Many $many);
}
