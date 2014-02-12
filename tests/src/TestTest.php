<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		Log::setEnabled(TRUE);

		$users = User::all()->executeAndLoad('posts');

		var_dump($users[0]->posts());
		var_dump($users[1]->posts());
		var_dump($users[2]->posts());
		var_dump($users[3]->posts());

		var_dump(Log::all());
	}

}
