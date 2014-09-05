<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Rel\HasManyAs;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasManyAs
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyAsTest extends AbstractDbTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     * @covers ::getForeignClassKey
     */
    public function testConstruct()
    {
        $rel = new HasManyAs('test', Country::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\City', 'parent');

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Country::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(City::getRepo(), $rel->getRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('parentId', $rel->getForeignKey());
        $this->assertSame('parentClass', $rel->getForeignClassKey());

        $rel = new HasManyAs(
            'test',
            City::getRepo()->getConfig(),
            Country::getRepo(),
            'parent',
            ['foreignKey' => 'test', 'foreignClassKey' => 'testClass']
        );
        $this->assertSame('test', $rel->getForeignKey());
        $this->assertSame('testClass', $rel->getForeignClassKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new HasManyAs('users', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Repo', 'location');

        $models = new Models([
            new City(),
            new City(),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new City(['id' => null]),
            new City(['id' => 2]),
        ]);

        $this->assertTrue($rel->hasModels($models));
    }

    /**
     * @covers ::loadModels
     * @covers ::findModels
     */
    public function testLoadModels()
    {
        $rel = new HasManyAs('users', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\User', 'location');

        $models = new Models([
            new City(['id' => 1]),
            new City(['id' => 2]),
            new City(['id' => 3]),
        ]);

        $users = $rel->loadModels($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\User', $users);
        $this->assertCount(2, $users);

        $this->assertEquals(1, $users[0]->id);
        $this->assertEquals(2, $users[1]->id);
    }

    public function dataAreLinked()
    {
        return [
            [
                new City(['id' => 2]),
                new User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\TestModel\City']),
                false,
            ],
            [
                new City(['id' => 12]),
                new User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']),
                false,
            ],
            [
                new City(['id' => 12]),
                new User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\TestModel\City']),
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
        $rel = new HasManyAs('users', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\User', 'location');

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasManyAs('users', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\User', 'location');

        $model = new City(['id' => 2]);
        $foreign1 = new User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\TestModel\City']);
        $foreign2 = new User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\TestModel\City']);
        $foreign3 = new User(['locationId' => 8, 'locationClass' => 'Harp\Harp\Test\TestModel\Country']);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($link);

        $this->assertEquals(null, $foreign1->locationId);
        $this->assertEquals(null, $foreign1->locationClass);
        $this->assertEquals(2, $foreign2->locationId);
        $this->assertEquals('Harp\Harp\Test\TestModel\City', $foreign2->locationClass);
        $this->assertEquals(2, $foreign3->locationId);
        $this->assertEquals('Harp\Harp\Test\TestModel\City', $foreign3->locationClass);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasManyAs('users', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\User', 'location');

        $select = new Select(City::getRepo());

        $rel->join($select, 'City');

        $this->assertEquals(
            'SELECT `City`.* FROM `City` JOIN `User` AS `users` ON `users`.`locationId` = `City`.`id` AND `users`.`locationClass` = "Harp\Harp\Test\TestModel\City" AND `users`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
