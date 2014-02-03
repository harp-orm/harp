<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field as F;
use CL\Luna\Rel as R;
use CL\Luna\Validator as V;

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
	 * @var integer
	 */
	public $address_id;

	/**
	 * @return Post
	 */
	public function address()
	{
		return parent::getRel('address');
	}

	/**
	 * @return Collection
	 */
	public function posts()
	{
		return parent::getRel('posts');
	}

	public static function CL_Luna_Test_User(Schema $config)
	{
		$config
			->setRels([
				'posts' => new R\HasMany('CL\Luna\Test\Post'),
				'address' => new R\BelongsTo('CL\Luna\Test\Address'),
			])
			->setValidators([
				'name' => [new V\Present()],
			])
			->setFields([
				'name' => new F\String(),
				'password' => new F\Password(),
			]);
	}

}
