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
	public function getAddress()
	{
		return parent::getLinkByName('address');
	}

	/**
	 * @return Collection
	 */
	public function getPosts()
	{
		return parent::getLinkByName('posts');
	}

	public static function test($model, $event)
	{
		var_dump('User event "test" called');
	}

	public static function CL_Luna_Test_User(Schema $schema)
	{
		$schema
			->setSoftDelete(TRUE)

			->setFields([
				new Integer('id'),
				new String('name'),
				new Password('password'),
			])

			->setRels([
				new BelongsTo('address', Address::getSchema()),
				new HasMany('posts', Post::getSchema()),
			])

			->setValidators([
				new Present('name')
			])

			->getEventListeners()
				->add(ModelEvent::SAVE, 'CL\Luna\Test\User::test');
	}

}
