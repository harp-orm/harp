<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$user = User::get(4);
		$post = Post::get(3);

		$user->posts()->add($post);
		$user->save();
	}

}
