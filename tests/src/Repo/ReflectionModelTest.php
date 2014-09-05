<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Repo\ReflectionModel;
use Harp\Harp\Repo\Container;

/**
 * @coversDefaultClass Harp\Harp\Repo\ReflectionModel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ConfigTest extends AbstractTestCase
{
    /**
     * @covers ::getPublicPropertyNames
     */
    public function testGetPublicPropertyNames()
    {
        $reflection = new ReflectionModel('Harp\Harp\Test\TestModel\City');

        $this->assertEquals(['id', 'name', 'countryId'], $reflection->getPublicPropertyNames());
    }

    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        Container::clear();

        $reflection = new ReflectionModel('Harp\Harp\Test\TestModel\City');

        $reflection->initialize(City::getRepo()->getConfig());
    }

    /**
     * @covers ::isRoot
     */
    public function testIsRoot()
    {
        $post = new ReflectionModel('Harp\Harp\Test\TestModel\Post');

        $this->assertTrue($post->isRoot());

        $blogPost = new ReflectionModel('Harp\Harp\Test\TestModel\BlogPost');

        $this->assertFalse($blogPost->isRoot());
    }

    /**
     * @covers ::getRoot
     */
    public function testGetRoot()
    {
        $post = new ReflectionModel('Harp\Harp\Test\TestModel\Post');

        $this->assertEquals('Harp\Harp\Test\TestModel\Post', $post->getRoot()->getName());

        $blogPost = new ReflectionModel('Harp\Harp\Test\TestModel\BlogPost');

        $this->assertEquals('Harp\Harp\Test\TestModel\Post', $blogPost->getRoot()->getName());
    }
}
