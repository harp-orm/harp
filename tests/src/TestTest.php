<?php namespace CL\Luna\Test;

class TestTest extends AbstractTestCase {

	public function testTest()
	{
		$users = User::all()->scope('unregistered');
		$user = $users->execute()->fetch();
		setlocale(LC_MESSAGES, 'en');
		putenv("LANG=en.utf8");
		bindtextdomain("luna", "../../locale");
		bind_textdomain_codeset('luna', 'UTF-8');
		textdomain("luna");
		var_dump(_('Field must be present'));
		var_dump($user);
	}

}
