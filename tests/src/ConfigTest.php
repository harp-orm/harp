<?php

namespace Harp\Harp\Test;

use Harp\Harp\Config;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\BlogPost;
use Harp\Harp\Repo\ReflectionModel;
use Harp\Harp\Repo\Event;

/**
 * @coversDefaultClass Harp\Harp\Config
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ConfigTest extends AbstractTestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals('User', $config->getName());
    }

    /**
     * @covers ::getRepo
     */
    public function testGetRepo()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\Post');

        $this->assertSame($config->getRepo(), Post::getRepo());

        $config = new Config(__NAMESPACE__.'\TestModel\BlogPost');

        $this->assertSame($config->getRepo(), BlogPost::getRepo());
    }

    /**
     * @covers ::getPrimaryKey
     * @covers ::setPrimaryKey
     */
    public function testPrimaryKey()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals('id', $config->getPrimaryKey());

        $config->setPrimaryKey('guid');

        $this->assertEquals('guid', $config->getPrimaryKey());
    }

    /**
     * @covers ::getNameKey
     * @covers ::setNameKey
     */
    public function testNameKey()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals('name', $config->getNameKey());

        $config->setNameKey('title');

        $this->assertEquals('title', $config->getNameKey());
    }

    /**
     * @covers ::setTable
     * @covers ::getTable
     */
    public function testTable()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals('User', $config->getTable());

        $config->setTable('users');

        $this->assertEquals('users', $config->getTable());
    }

    /**
     * @covers ::setDb
     * @covers ::getDb
     */
    public function testDb()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals('default', $config->getDb());

        $config->setDb('test');

        $this->assertEquals('test', $config->getDb());
    }

    /**
     * @covers ::getReflectionModel
     */
    public function testGetReflectionModel()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\User');

        $this->assertEquals(new ReflectionModel(__NAMESPACE__.'\TestModel\User'), $config->getReflectionModel());
    }

    /**
     * @covers ::getFields
     * @covers ::setFields
     */
    public function testFields()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\City');

        $this->assertEquals(['id', 'name', 'countryId'], $config->getFields());

        $config->setFields(['id']);

        $this->assertEquals(['id'], $config->getFields());
    }

    /**
     * @covers ::getSoftDelete
     * @covers ::setSoftDelete
     */
    public function testSoftDelete()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\City');

        $this->assertEquals(false, $config->getSoftDelete());

        $config->setSoftDelete(true);

        $this->assertEquals(true, $config->getSoftDelete());
    }

    /**
     * @covers ::getInherited
     * @covers ::setInherited
     * @covers ::isRoot
     * @covers ::getRootConfig
     */
    public function testInherited()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\City');

        $this->assertEquals(false, $config->getInherited());
        $this->assertEquals(__NAMESPACE__.'\TestModel\City', $config->getRootConfig()->getModelClass());
        $this->assertTrue($config->isRoot());

        $config = new Config(__NAMESPACE__.'\TestModel\BlogPost');
        $this->assertFalse($config->isRoot());

        $this->assertEquals(true, $config->getInherited());
        $this->assertEquals(__NAMESPACE__.'\TestModel\Post', $config->getRootConfig()->getModelClass());
    }

    /**
     * @covers ::getModelClass
     */
    public function testModelClass()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\City');

        $this->assertEquals(__NAMESPACE__.'\TestModel\City', $config->getModelClass());
    }

    /**
     * @covers ::isModel
     */
    public function testIsModel()
    {
        $postConfig = Post::getRepo()->getConfig();
        $blogPostConfig = BlogPost::getRepo()->getConfig();

        $post = new Post();
        $blogPost = new BlogPost();

        $this->assertTrue($postConfig->isModel($post));
        $this->assertTrue($postConfig->isModel($blogPost));
        $this->assertTrue($blogPostConfig->isModel($post));
        $this->assertTrue($blogPostConfig->isModel($blogPost));

        $city = new City();

        $this->assertFalse($postConfig->isModel($city));
    }

    /**
     * @covers ::assertModel
     */
    public function testAssertModel()
    {
        $config = new Config(__NAMESPACE__.'\TestModel\City');
        $model = new City();
        $other = new Post();

        $config->assertModel($model);

        $this->setExpectedException('InvalidArgumentException');

        $config->assertModel($other);
    }
}
