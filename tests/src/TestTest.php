<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Atlas\Query\InsertQuery;
use CL\Luna\EntityManager\EntityManager;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		Log::setEnabled(TRUE);

		$posts = Post::all()->loadWith(['user' => 'address']);

		var_dump($posts[0]->user()->address());

		$post = Post::get(1);
		$post->temp_stuff = 'asdasd';
		$post->temp_stuff2 = 'asdasd2';

		$post2 = Post::get(2);
		$post2->title = "new title 111";


		$post3 = Post::get(3);
		// $post3->title = "new title 222";

		$user = new User(['name' => 'newly saved']);
		$user->save();

		var_dump(
			$post, $post2, $post3);

		$user->posts()->set([$post, $post2, $post3]);

		EntityManager::getInstance()->preserve($user);

		var_dump(Log::all());
	}

}
