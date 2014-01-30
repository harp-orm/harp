<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$users = User::all()->scope('unregistered');
		$user = $users->execute()->fetch();
		$user->name = NULL;
		$user->validate();
		var_dump($user->getErrors());

		var_dump($user);
	}

}
