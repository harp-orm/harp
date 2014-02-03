<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$user = new User(['name' => 'test 12', 'address_id' => 10]);
		$user->save();
	}

}
