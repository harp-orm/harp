<?php namespace CL\Luna\Rel\Feature;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelCollection;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface MultiInterface
{
	public function getKey();
	public function getForeignKey();
	public function getSelect();

	public function update(Model $parent, ModelCollection $foreign);
}
