<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$users = User::all()->scope('unregistered');
		var_dump(User::getFields());
		$user = $users->execute()->fetch();

		$user->name = "asd";
		$user->password = 12;

		var_dump($user->getChanges());
	}

}
