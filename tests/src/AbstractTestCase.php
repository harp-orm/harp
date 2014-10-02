<?php

namespace Harp\Harp\Test;

use Harp\Harp\Repo\Container;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    public function getDb()
    {
        return $this->db;
    }

    public function setUp()
    {
        $this->db = new Session();
        $this->db->setAliases([
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

        Container::clear();
    }
}
