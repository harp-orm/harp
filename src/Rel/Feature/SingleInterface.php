<?php namespace CL\Luna\Rel\Feature;

use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface SingleInterface
{
	public function getKey();
	public function getForeignKey();
	public function getSelect();

	public function update(Model $parent, Model $model);
}
