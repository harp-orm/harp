<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\Model;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;
use Harp\Harp\Rel\BelongsTo;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\BelongsTo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsToTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new BelongsTo('test', Model\City::getRepo(), Model\Country::getRepo());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Model\City::getRepo(), $rel->getRepo());
        $this->assertSame(Model\Country::getRepo(), $rel->getForeignRepo());
        $this->assertSame('testId', $rel->getKey());
        $this->assertSame('id', $rel->getForeignKey());

        $rel = new BelongsTo('test', Model\City::getRepo(), Model\Country::getRepo(), array('key' => 'test'));
        $this->assertSame('test', $rel->getKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new BelongsTo('country', Model\City::getRepo(), Model\Country::getRepo());

        $models = new Models([
            new Model\City(),
            new Model\City(),
        ]);

        $this->assertFalse($rel->hasForeign($models));

        $models = new Models([
            new Model\City(['countryId' => null]),
            new Model\City(['countryId' => 2]),
        ]);

        $this->assertTrue($rel->hasForeign($models));
    }

    /**
     * @covers ::loadForeign
     */
    public function testLoadForeign()
    {
        $rel = new BelongsTo('country', Model\City::getRepo(), Model\Country::getRepo());

        $models = new Models([
            new Model\City(['countryId' => null]),
            new Model\City(['countryId' => 1]),
            new Model\City(['countryId' => 2]),
        ]);

        $countries = $rel->loadForeign($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\Model\Country', $countries);
        $this->assertCount(2, $countries);

        $this->assertEquals(1, $countries[0]->id);
        $this->assertEquals(2, $countries[1]->id);
    }

    public function dataAreLinked()
    {
        return [
            [new Model\City(['countryId' => 2]), new Model\Country(), false],
            [new Model\City(['countryId' => 2]), new Model\Country(['id' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new BelongsTo('country', Model\City::getRepo(), Model\Country::getRepo());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new BelongsTo('country', Model\City::getRepo(), Model\Country::getRepo());

        $model = new Model\City(['countryId' => 2]);
        $foreign = new Model\Country(['id' => 20]);
        $link = new LinkOne($model, $rel, $foreign);

        $rel->update($link);

        $this->assertEquals(20, $model->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new BelongsTo('country', Model\City::getRepo(), Model\Country::getRepo());

        $select = new Select(Model\City::getRepo());

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
        $rel = new BelongsTo('user', Model\Address::getRepo(), Model\User::getRepo());

        $select = new Select(Model\Address::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `user` ON `user`.`id` = `Address`.`userId` AND `user`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
