<?php namespace CL\Luna\Test;

use CL\Luna\Model as M;
use CL\Luna\Field\String;
use CL\Luna\Rel\HasMany;
use CL\Luna\Validator\Present;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends M\Model {

	use M\SchemaTrait;

	/**
	 * @var string
	 */
	public $zip_code;

	/**
	 * @var string
	 */
	public $locatoion;

	/**
	 * @return Post
	 */
	public function users()
	{
		return parent::getRel('users');
	}

	public static function CL_Luna_Test_User(M\Schema $config)
	{
		$config
			->setRels([
				'users' => new HasMany('CL\Luna\Test\User'),
			])
			->setValidators([
				'locatoion' => [new Present()],
			])
			->setFields([
				'zip_code' => new String(),
				'locatoion' => new String(),
			]);
	}

}
