<?php namespace CL\Luna\Test;

use CL\Luna\Schema\Schema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait Nested {

	/**
	 * @var string
	 */
	public $parent;

	public static function CL_Luna_Test_Nested(Schema $schema)
	{
		$schema
			->getFields()
				->add(new \CL\Luna\Field\String('parent'));
	}

	/**
	 * @event save
	 */
	public function applyNested()
	{
		echo 'do stuff';
	}
}
