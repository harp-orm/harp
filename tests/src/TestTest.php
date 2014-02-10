<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$result = Address::all()->executeAndLoad(['users']);
		foreach ($result as $address)
		{
			var_dump($address, $address->users());
		}
	}

}
