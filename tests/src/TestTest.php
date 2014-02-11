<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$result = Post::all()->executeAndLoad(['user' => ['address']]);
		var_dump($result[0]->user()->address());
	}

}
