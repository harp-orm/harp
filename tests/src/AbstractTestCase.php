<?php

namespace Harp\Harp\Test;

use Harp\Harp\Session;
use Harp\Query\DB;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    public function getSession()
    {
        return $this->session;
    }

    public function getDb()
    {
        return $this->getSession()->getDb();
    }

    public function setUp()
    {
        $db = new DB(
            'mysql:dbname=harp-orm/harp;host=127.0.0.1',
            'root',
            '',
            ['escaping' => DB::ESCAPING_MYSQL]
        );

        $db->setLogger(new TestLogger());

        $this->session = new Session($db, [
            'Address'  => __NAMESPACE__.'\TestModel\Address',
            'BlogPost' => __NAMESPACE__.'\TestModel\BlogPost',
            'City'     => __NAMESPACE__.'\TestModel\City',
            'Country'  => __NAMESPACE__.'\TestModel\Country',
            'Post'     => __NAMESPACE__.'\TestModel\Post',
            'PostTag'  => __NAMESPACE__.'\TestModel\PostTag',
            'Profile'  => __NAMESPACE__.'\TestModel\Profile',
            'Tag'      => __NAMESPACE__.'\TestModel\Tag',
            'User'     => __NAMESPACE__.'\TestModel\User',
        ]);

        parent::setUp();
    }
}
