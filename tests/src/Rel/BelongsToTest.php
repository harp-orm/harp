<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Harp\Rel\BelongsTo;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\BelongsTo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsToTest extends AbstractDbTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new BelongsTo('test', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $this->assertSame('test', $rel->getName());
        $this->assertSame(City::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(Country::getRepo(), $rel->getRepo());
        $this->assertSame('testId', $rel->getKey());
        $this->assertSame('id', $rel->getForeignKey());

        $rel = new BelongsTo('test', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country', array('key' => 'test'));
        $this->assertSame('test', $rel->getKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new BelongsTo('country', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $models = new Models([
            new City(),
            new City(),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new City(['countryId' => null]),
            new City(['countryId' => 2]),
        ]);

        $this->assertTrue($rel->hasModels($models));
    }

    /**
     * @covers ::loadModels
     * @covers ::findModels
     */
    public function testLoadModels()
    {
        $rel = new BelongsTo('country', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $models = new Models([
            new City(['countryId' => null]),
            new City(['countryId' => 1]),
            new City(['countryId' => 2]),
        ]);

        $countries = $rel->loadModels($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\Country', $countries);
        $this->assertCount(2, $countries);

        $this->assertEquals(1, $countries[0]->id);
        $this->assertEquals(2, $countries[1]->id);
    }

    public function dataAreLinked()
    {
        return [
            [new City(['countryId' => 2]), new Country(), false],
            [new City(['countryId' => 2]), new Country(['id' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new BelongsTo('country', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new BelongsTo('country', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $model = new City(['countryId' => 2]);
        $foreign = new Country(['id' => 20]);
        $link = new LinkOne($model, $rel, $foreign);

        $rel->update($link);

        $this->assertEquals(20, $model->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new BelongsTo('country', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\Country');

        $select = new Select(City::getRepo());

        $rel->join($select, 'City');

        $this->assertEquals(
            'SELECT `City`.* FROM `City` JOIN `Country` AS `country` ON `country`.`id` = `City`.`countryId`',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $rel = new BelongsTo('user', Address::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\User');

        $select = new Select(Address::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `user` ON `user`.`id` = `Address`.`userId` AND `user`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
