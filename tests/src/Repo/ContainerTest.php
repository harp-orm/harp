<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Repo;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Repo\Container;

/**
 * @coversDefaultClass Harp\Harp\Repo\Container
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ContainerTest extends AbstractTestCase
{
    /**
     * @covers ::get
     * @covers ::has
     * @covers ::set
     * @covers ::clear
     */
    public function testGetterSetter()
    {
        $class = 'Harp\Harp\Test\TestModel\City';

        $this->assertFalse(Container::has($class));
        $repo = Container::get($class);

        $this->assertTrue(Container::has($class));

        $this->assertInstanceOf('Harp\Harp\Repo', $repo);
        $this->assertEquals($class, $repo->getModelClass());

        $this->assertSame($repo, Container::get($class));

        $repo2 = new Repo($class);

        Container::set($class, $repo2);

        $this->assertSame($repo2, Container::get($class));

        Container::clear();

        $this->assertFalse(Container::has($class));
    }

    /**
     * @covers ::getActualClass
     * @covers ::setActualClass
     * @covers ::hasActualClass
     */
    public function testActualClasses()
    {
        $class = 'Harp\Harp\Test\TestModel\City';
        $actual = 'Harp\Harp\Test\TestModel\City';

        $this->assertFalse(Container::hasActualClass($class));

        Container::setActualClass($class, $actual);

        $this->assertTrue(Container::hasActualClass($class));

        $this->assertEquals($actual, Container::getActualClass($class));

        Container::clear();

        $this->assertFalse(Container::hasActualClass($class));
    }

    /**
     * @covers ::get
     */
    public function testGetActualClasses()
    {
        $class = 'Harp\Harp\Test\TestModel\Country';
        $actual = 'Harp\Harp\Test\TestModel\City';

        $this->assertEquals($class, Container::get($class)->getModelClass());

        Container::setActualClass($class, $actual);

        $this->assertEquals($class, Container::get($class)->getModelClass());

        Container::clear();
        Container::setActualClass($class, $actual);

        $this->assertEquals($actual, Container::get($class)->getModelClass());

        Container::clear();

        $this->assertEquals($class, Container::get($class)->getModelClass());
    }
}
