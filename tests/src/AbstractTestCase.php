<?php

namespace CL\Luna\Test;

use CL\EnvBackup\Env;
use CL\EnvBackup\StaticParam;
use CL\Atlas\DB;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase {

    /**
     * @var Env
     */
    protected $env;

    /**
     * @var TestLogger
     */
    protected $logger;

    /**
     * @return Env
     */
    public function getEnv()
    {
        return $this->env;
    }
    /**
     * @return TestLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function setUp()
    {
        parent::setUp();

        $this->env = new Env();
        $this->env
            ->add(new StaticParam('CL\Luna\Mapper\MainRepo', 'repo', null))
            ->apply();

        $this->logger = new TestLogger();

        DB::setConfig('default', array(
            'dsn' => 'mysql:dbname=test-luna;host=127.0.0.1',
            'username' => 'root',
        ));

        DB::get()->execute('ALTER TABLE Post AUTO_INCREMENT = 5', array());
        DB::get()->setLogger($this->logger);
        DB::get()->beginTransaction();
    }

    public function tearDown()
    {
        $this->env->restore();

        DB::get()->rollback();

        parent::tearDown();
    }
}
