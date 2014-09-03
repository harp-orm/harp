<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Rel\HasMany;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasMany
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyTest extends AbstractDbTestCase
{
    /**
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new HasMany('test', Country::getRepo()->getConfig(), City::getRepo());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Country::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(City::getRepo(), $rel->getRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('countryId', $rel->getForeignKey());

        $rel = new HasMany('test', City::getRepo()->getConfig(), Country::getRepo(), array('foreignKey' => 'test'));
        $this->assertSame('test', $rel->getForeignKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new HasMany('cities', Country::getRepo()->getConfig(), City::getRepo());

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
     * @covers ::findModels
     */
    public function testModels()
    {
        $rel = new HasMany('cities', Country::getRepo()->getConfig(), City::getRepo());

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
            [new Country(['id' => 2]), new City(['countryId' => 12]), false],
            [new Country(['id' => 2]), new City(['countryId' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new HasMany('cities', Country::getRepo()->getConfig(), City::getRepo());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasMany('cities', Country::getRepo()->getConfig(), City::getRepo());

        $model = new Country(['id' => 2]);
        $foreign1 = new City(['countryId' => 2]);
        $foreign2 = new City(['countryId' => 2]);
        $foreign3 = new City(['countryId' => 8]);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($link);

        $this->assertEquals(null, $foreign1->countryId);
        $this->assertEquals(2, $foreign2->countryId);
        $this->assertEquals(2, $foreign3->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasMany('cities', Country::getRepo()->getConfig(), City::getRepo());

        $select = new Select(Country::getRepo());

        $rel->join($select, 'Country');

        $this->assertEquals(
            'SELECT `Country`.* FROM `Country` JOIN `City` AS `cities` ON `cities`.`countryId` = `Country`.`id`',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $rel = new HasMany('users', Address::getRepo()->getConfig(), User::getRepo());

        $select = new Select(Address::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `users` ON `users`.`addressId` = `Address`.`id` AND `users`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
