<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Harp\Rel\BelongsToPolymorphic;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\BelongsToPolymorphic
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsToPolymorphicTest extends AbstractDbTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getClassKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new BelongsToPolymorphic('test', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $this->assertSame('test', $rel->getName());
        $this->assertSame(User::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(Country::getRepo(), $rel->getRepo());
        $this->assertSame('testId', $rel->getKey());
        $this->assertSame('testClass', $rel->getClassKey());
        $this->assertSame('id', $rel->getForeignKey());

        $rel = new BelongsToPolymorphic(
            'test',
            City::getRepo()->getConfig(),
            'Harp\Harp\Test\TestModel\Country',
            ['key' => 'test', 'classKey' => 'testClass']
        );

        $this->assertSame('test', $rel->getKey());
        $this->assertSame('testClass', $rel->getClassKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new BelongsToPolymorphic('test', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $models = new Models([
            new City(),
            new City(),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new City(['testId' => 1, 'testClass' => null]),
            new City(['testId' => 2, 'testClass' => null]),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new City(['testId' => 1, 'testClass' => 'Harp\Harp\Test\TestModel\Country']),
            new City(['testId' => 2, 'testClass' => 'Harp\Harp\Test\TestModel\City']),
        ]);

        $this->assertTrue($rel->hasModels($models));

    }

    /**
     * @covers ::loadModels
     */
    public function testLoadModels()
    {
        $rel = new BelongsToPolymorphic('location', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $models = new Models([
            new User(['locationId' => null, 'locationClass' => null]),
            new User(['locationId' => null, 'locationClass' => 'Harp\Harp\Test\TestModel\Post']),
            new User(['locationId' => 1, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']),
            new User(['locationId' => 1, 'locationClass' => 'Harp\Harp\Test\TestModel\City']),
        ]);

        $locations = $rel->loadModels($models);

        $this->assertCount(2, $locations);

        $this->assertEquals(1, $locations[0]->id);
        $this->assertInstanceOf('Harp\Harp\Test\TestModel\Country', $locations[0]);

        $this->assertEquals(1, $locations[1]->id);
        $this->assertInstanceOf('Harp\Harp\Test\TestModel\City', $locations[1]);
    }

    public function dataAreLinked()
    {
        return [
            [
                new User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']),
                new City(['id' => 2]),
                false,
            ],
            [
                new User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']),
                new Country(),
                false,
            ],
            [
                new User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']),
                new Country(['id' => 2]),
                true,
            ],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new BelongsToPolymorphic('location', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new BelongsToPolymorphic('location', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $model = new User();
        $foreign = new Country(['id' => 20]);
        $link = new LinkOne($model, $rel, $foreign);

        $rel->update($link);

        $this->assertEquals(20, $model->locationId);
        $this->assertEquals('Harp\Harp\Test\TestModel\Country', $model->locationClass);
    }

    /**
     * @covers ::join
     * @expectedException BadMethodCallException
     */
    public function testJoin()
    {
        $rel = new BelongsToPolymorphic('location', User::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $select = new Select(User::getRepo());

        $rel->join($select, 'City');
    }
}
