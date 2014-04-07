<?php namespace CL\Luna\Test;

use CL\EnvBackup as EB;
use CL\Atlas\DB;
use CL\Luna\Util\Log;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase {

    public $env;

    public function setUp()
    {
        parent::setUp();

        Log::setEnabled(TRUE);

        $this->env = new EB\Env([
            new EB\StaticParam('CL\Luna\Util\Log', 'items', [])
        ]);

        DB::setConfig('default', array(
            'dsn' => 'mysql:dbname=test-luna;host=127.0.0.1',
            'username' => 'root',
        ));

        DB::get()->beginTransaction();
    }

    public function tearDown()
    {
        $this->env->restore();

        DB::get()->rollback();

        parent::tearDown();
    }
}
