<?php namespace CL\Luna\Test;

use CL\Luna\Model as M;
use CL\Luna\Field\String;
use CL\Luna\Field\Integer;
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
	 * @var integer
	 */
	public $id;

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

	public static function CL_Luna_Test_Address(M\Schema $config)
	{
		$config
			->setRels([
				'users' => new HasMany(User::getSchema()),
			])
			->setValidators([
				'locatoion' => [new Present()],
			])
			->setFields([
				'id' => new Integer(),
				'zip_code' => new String(),
				'locatoion' => new String(),
			]);
	}

}
