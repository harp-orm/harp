<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field\String;
use CL\Luna\Field\Integer;
use CL\Luna\Rel\HasMany;
use CL\Luna\Validator\Present;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends Model {

	use SchemaTrait;

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
		return parent::getOrLoadLink('users');
	}

	public static function CL_Luna_Test_Address(Schema $schema)
	{
		$schema
			->setRels([
				new HasMany('users', User::getSchema()),
			])
			->setValidators([
				new Present('locatoion'),
			])
			->setFields([
				new Integer('id'),
				new String('zip_code'),
				new String('location'),
			]);
	}

}
