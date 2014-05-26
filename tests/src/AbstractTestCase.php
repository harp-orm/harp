<?php

namespace CL\Luna\Test;

use CL\Atlas\DB;
use CL\Luna\Test\Repo;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase {

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

        DB::setConfig('default', array(
            'dsn' => 'mysql:dbname=test-luna;host=127.0.0.1',
            'username' => 'root',
        ));

        DB::get()->execute('ALTER TABLE Post AUTO_INCREMENT = 5', array());
        DB::get()->execute('ALTER TABLE PostTag AUTO_INCREMENT = 4', array());
        DB::get()->setLogger($this->logger);
        DB::get()->beginTransaction();

        Repo\Address::get()->getIdentityMap()->clear();
        Repo\BlogPost::get()->getIdentityMap()->clear();
        Repo\City::get()->getIdentityMap()->clear();
        Repo\Country::get()->getIdentityMap()->clear();
        Repo\Post::get()->getIdentityMap()->clear();
        Repo\PostTag::get()->getIdentityMap()->clear();
        Repo\Profile::get()->getIdentityMap()->clear();
        Repo\Tag::get()->getIdentityMap()->clear();
        Repo\User::get()->getIdentityMap()->clear();
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
