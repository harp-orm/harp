<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field\Integer;
use CL\Luna\Field\String;
use CL\Luna\Rel\BelongsTo;
use CL\Luna\Validator\Present;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends Model {

	use SchemaTrait;

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $body;

	/**
	 * @return Post
	 */
	public function user()
	{
		return parent::getRel('user');
	}

	public static function CL_Luna_Test_Post(Schema $config)
	{
		$config
			->setRels([
				'user' => new BelongsTo(User::getSchema()),
			])
			->setValidators([
				'title' => [new Present()],
			])
			->setFields([
				'id' => new Integer(),
				'title' => new String(),
				'body' => new String(),
			]);
	}

}
