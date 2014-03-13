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

		Log::setEnabled(TRUE);

		$this->env = new EB\Environment([
			'static' => new EB\Environment_Group_Static,
		]);

		$this->env->backup_and_set([
			'CL\Luna\Util\Log::$items' => []
		]);

		DB::configuration('default', array(
			'dsn' => 'mysql:dbname=test-luna;host=127.0.0.1',
			'username' => 'root',
		));

		DB::instance()->beginTransaction();
	}

	public function tearDown()
	{
		$this->env->restore();

		DB::instance()->rollback();

		parent::tearDown();
	}
}
