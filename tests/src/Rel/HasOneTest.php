<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Harp\Rel\HasOne;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasOne
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasOneTest extends AbstractDbTestCase
{
    /**
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new HasOne('test', Country::getRepo()->getConfig(), City::getRepo());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Country::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(City::getRepo(), $rel->getRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('countryId', $rel->getForeignKey());

        $rel = new HasOne('test', Country::getRepo()->getConfig(), City::getRepo(), array('foreignKey' => 'test'));
        $this->assertSame('test', $rel->getForeignKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new HasOne('city', Country::getRepo()->getConfig(), City::getRepo());

        $models = new Models([
            new Country(),
            new Country(),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new Country(['id' => null]),
            new Country(['id' => 2]),
        ]);

        $this->assertTrue($rel->hasModels($models));
    }

    /**
     * @covers ::loadModels
     */
    public function testLoadModels()
    {
        $rel = new HasOne('city', Country::getRepo()->getConfig(), City::getRepo());

        $models = new Models([
            new Country(['id' => 1]),
            new Country(['id' => 2]),
        ]);

        $cities = $rel->loadModels($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\City', $cities);
        $this->assertCount(4, $cities);

        $this->assertEquals(1, $cities[0]->id);
        $this->assertEquals(2, $cities[1]->id);
        $this->assertEquals(3, $cities[2]->id);
        $this->assertEquals(4, $cities[3]->id);
    }

    public function dataAreLinked()
    {
        return [
            [new Country(['id' => 2]), new City(), false],
            [new Country(['id' => 2]), new City(['countryId' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new HasOne('city', Country::getRepo()->getConfig(), City::getRepo());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasOne('city', Country::getRepo()->getConfig(), City::getRepo());

        $model = new Country(['id' => 20]);
        $old = new City(['countryId' => 20]);
        $foreign = new City(['countryId' => 2]);
        $link = new LinkOne($model, $rel, $old);
        $link->set($foreign);

        $rel->update($link);

        $this->assertEquals(20, $foreign->countryId);
        $this->assertNull($old->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasOne('city', Country::getRepo()->getConfig(), City::getRepo());

        $select = new Select(Country::getRepo());

        $rel->join($select, 'Country');

        $this->assertEquals(
            'SELECT `Country`.* FROM `Country` JOIN `City` AS `city` ON `city`.`countryId` = `Country`.`id`',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $rel = new HasOne('user', Address::getRepo()->getConfig(), User::getRepo());

        $select = new Select(Address::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `user` ON `user`.`addressId` = `Address`.`id` AND `user`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
