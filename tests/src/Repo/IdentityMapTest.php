<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\IdentityMap;
use Harp\Harp\Repo;
use Harp\Harp\Model\State;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;

/**
 * @coversDefaultClass Harp\Harp\Repo\IdentityMap
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class IdentityMapTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     * @covers ::getModels
     */
    public function testConstruct()
    {
        $repo = new Repo('Harp\Harp\Test\TestModel\City');
        $map = new IdentityMap($repo);

        $this->assertSame($repo, $map->getRepo());
        $this->assertSame([], $map->getModels());
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $map = City::getRepo()->getIdentityMap()->clear();

        $model1 = new City(['id' => 1], State::SAVED);
        $model2 = new City(['id' => 1], State::SAVED);
        $model3 = new City(['id' => 2], State::SAVED);

        $this->assertSame($model1, $map->get($model1));
        $this->assertSame($model1, $map->get($model2));
        $this->assertSame($model3, $map->get($model3));
    }

    /**
     * @covers ::getArray
     */
    public function testGetArray()
    {
        $map = City::getRepo()->getIdentityMap()->clear();

        $model1 = new City(['id' => 1], State::SAVED);
        $model2 = new City(['id' => 2], State::SAVED);

        $map->get($model1);
        $map->get($model2);

        $models = [
            new City(['id' => 1], State::SAVED),
            new City(['id' => 2], State::SAVED),
        ];

        $expected = [$model1, $model2];

        $this->assertSame($expected, $map->getArray($models));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $map = City::getRepo()->getIdentityMap()->clear();

        $model = new City(['id' => 1], State::SAVED);

        $this->assertFalse($map->has($model));

        $map->get($model);

        $this->assertTrue($map->has($model));
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $map = City::getRepo()->getIdentityMap()->clear();

        $map->get(new City(['id' => 1], State::SAVED));
        $this->assertCount(1, $map->getModels());
        $map->clear();
        $this->assertCount(0, $map->getModels());
    }
}
