<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends Model {

	use SchemaTrait;
	use Nested;

	public static function scopeUnregistered($query)
	{
		return $query->where('user.address_id != ""');
	}

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @return Post
	 */
	public function address()
	{
		return $this->loadAssociation('address');
	}

	/**
	 * @return Collection
	 */
	public function posts()
	{
		return $this->loadAssociation('post');
	}

	public static function CL_Luna_Test_User(Schema $config)
	{
		$config
			->setFields([
				'name' => new \CL\Luna\Field\String(),
				'password' => new \CL\Luna\Field\Password(),
			]);
	}

}
