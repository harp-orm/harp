<?php namespace CL\Luna\Test;

use Openbuildings\EnvironmentBackup as EB;
use CL\Atlas\DB;
use CL\Luna\Util\Log;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase {

	public $env;

	public static function setUpBeforeClass()
	{
		setlocale(LC_MESSAGES, 'en_US');
		bindtextdomain("luna", __DIR__."/../../locale");
	}

	public function setUp()
	{
		parent::setUp();

		$this->env = new EB\Environment(array(
			'static' => new EB\Environment_Group_Static,
		));

		DB::configuration('default', array(
			'dsn' => 'mysql:dbname=test-luna;host=127.0.0.1',
			'username' => 'root',
		));

		Log::setEnabled(TRUE);
	}

	public function tearDown()
	{
		$this->env->restore();

		parent::tearDown();
	}
}