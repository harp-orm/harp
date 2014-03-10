<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Atlas\Query\InsertQuery;
use CL\Luna\Repo\Repo;

class TestTest extends AbstractTestCase {

	public function testPreserve()
	{
		Log::setEnabled(TRUE);

		$user3 = User::get(3);

		$user3->massAssign([
			'posts' => [
				[
					'title' => 'my title',
					'body' => 'my body',
				],
				[
					'title' => 'my title 2',
					'body' => 'my body 2',
				]
			],
			'address' => [
				'id' => 1,
				'zip_code' => 2222,
			]
		]);

		Repo::getInstance()->preserve($user3);

		var_dump(Log::all());
	}

	public function testLoadWith()
	{
		Log::setEnabled(TRUE);

		$posts = Post::all()->loadWith(['user' => 'address']);

		$user1 = $posts[0]->getUser();
		$user2 = $posts[1]->getUser();

		$address1 = $posts[0]->getUser()->address();
		$address2 = $posts[1]->getUser()->address();

		$this->assertEquals(
			[
				'SELECT post.* FROM post',
				'SELECT user.* FROM user WHERE (user.deleted_at IS NULL) AND (id IN ("1", "4", "5"))',
				'SELECT address.* FROM address WHERE (id IN ("1"))',
			],
			Log::all()
		);

		$this->assertSame($address1, $address2);
		$this->assertEquals(
			[
				'id' => 1,
				'name' => "User 1",
				'password' => NULL,
				'address_id' => "1",
				'parent' => NULL
			],
			$user1->getProperties()
		);

		$this->assertEquals(
			[
				'id' => 4,
				'name' => "User 4",
				'password' => NULL,
				'address_id' => "1",
				'parent' => NULL
			],
			$user2->getProperties()
		);
	}

}
