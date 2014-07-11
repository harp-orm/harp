<?php

namespace Harp\Harp\Test;

use Harp\Query\DB;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractDbTestCase extends AbstractTestCase {

    /**
     * @var TestLogger
     */
    protected $logger;

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

        $this->logger = new TestLogger();

        DB::setConfig([
            'dsn' => 'mysql:dbname=harp-orm/harp;host=127.0.0.1',
            'username' => 'root',
            'escaping' => DB::ESCAPING_MYSQL,
        ]);

        DB::get()->execute('ALTER TABLE Post AUTO_INCREMENT = 5');
        DB::get()->execute('ALTER TABLE PostTag AUTO_INCREMENT = 4');
        DB::get()->setLogger($this->logger);
        DB::get()->beginTransaction();
    }

    public function tearDown()
    {
        DB::get()->rollback();

        parent::tearDown();
    }

    public function assertQueries(array $query)
    {
        $this->assertEquals($query, $this->getLogger()->getEntries());
    }
}
