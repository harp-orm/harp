<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field\Integer;
use CL\Luna\Field\String;
use CL\Luna\Field\Password;
use CL\Luna\Rel\BelongsTo;
use CL\Luna\Rel\HasMany;
use CL\Luna\Validator\Present;
use CL\Luna\Event\ModelEvent;

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
	 * @var integer
	 */
	public $id;

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

	public static function test($model, $event)
	{
		var_dump('event');
	}

	public static function CL_Luna_Test_User(Schema $schema)
	{
		$schema
			->getFields()
				->add(new Integer('id'))
				->add(new String('name'))
				->add(new Password('password'));

		$schema
			->getRels()
				->add(new BelongsTo('address', Address::getSchema(), ['inverseOf' => 'user']))
				->add(new HasMany('posts', Post::getSchema()));

		$schema
			->getValidators()
				->add(new Present('name'));

		$schema
			->getEventListeners()
				->add(ModelEvent::SAVE, 'CL\Luna\Test\User::test');
	}

}
