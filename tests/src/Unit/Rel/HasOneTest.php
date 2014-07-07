<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;
use Harp\Harp\Rel\HasOne;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasOne
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasOneTest extends AbstractTestCase
{
    /**
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new HasOne('test', Model\Country::getRepo(), Model\City::getRepo());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Model\Country::getRepo(), $rel->getRepo());
        $this->assertSame(Model\City::getRepo(), $rel->getForeignRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('countryId', $rel->getForeignKey());

        $rel = new HasOne('test', Model\Country::getRepo(), Model\City::getRepo(), array('foreignKey' => 'test'));
        $this->assertSame('test', $rel->getForeignKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new HasOne('city', Model\Country::getRepo(), Model\City::getRepo());

        $models = new Models([
            new Model\Country(),
            new Model\Country(),
        ]);

        $this->assertFalse($rel->hasForeign($models));

        $models = new Models([
            new Model\Country(['id' => null]),
            new Model\Country(['id' => 2]),
        ]);

        $this->assertTrue($rel->hasForeign($models));
    }

    /**
     * @covers ::loadForeign
     */
    public function testLoadForeign()
    {
        $rel = new HasOne('city', Model\Country::getRepo(), Model\City::getRepo());

        $models = new Models([
            new Model\Country(['id' => 1]),
            new Model\Country(['id' => 2]),
        ]);

        $cities = $rel->loadForeign($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\Model\City', $cities);
        $this->assertCount(4, $cities);

        $this->assertEquals(1, $cities[0]->id);
        $this->assertEquals(2, $cities[1]->id);
        $this->assertEquals(3, $cities[2]->id);
        $this->assertEquals(4, $cities[3]->id);
    }

    public function dataAreLinked()
    {
        return [
            [new Model\Country(['id' => 2]), new Model\City(), false],
            [new Model\Country(['id' => 2]), new Model\City(['countryId' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new HasOne('city', Model\Country::getRepo(), Model\City::getRepo());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasOne('city', Model\Country::getRepo(), Model\City::getRepo());

        $model = new Model\Country(['id' => 20]);
        $old = new Model\City(['countryId' => 20]);
        $foreign = new Model\City(['countryId' => 2]);
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
        $rel = new HasOne('city', Model\Country::getRepo(), Model\City::getRepo());

        $select = new Select(Model\Country::getRepo());

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
        $rel = new HasOne('user', Model\Address::getRepo(), Model\User::getRepo());

        $select = new Select(Model\Address::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `user` ON `user`.`addressId` = `Address`.`id` AND `user`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
