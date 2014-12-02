<?php

namespace Harp\Harp\Test;

use Harp\Query\DB;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractDbTestCase extends AbstractTestCase {

    public function setUp()
    {
        parent::setUp();

        $db = $this->getDb();

        $db->execute('ALTER TABLE Post AUTO_INCREMENT = 5')
        $db->execute('ALTER TABLE PostTag AUTO_INCREMENT = 4')
        $db->beginTransaction();
    }

    public function tearDown()
    {
        $this->getDb()->rollback();

        parent::tearDown();
    }

    public function assertQueries(array $query)
    {
        $this->assertEquals($query, $this->getLogger()->getEntries());
    }
}
