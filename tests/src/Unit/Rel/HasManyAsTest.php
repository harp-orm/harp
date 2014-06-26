<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Model\Models;
use Harp\Harp\Rel\HasManyAs;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasManyAs
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyAsTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     * @covers ::getForeignClassKey
     */
    public function testConstruct()
    {
        $rel = new HasManyAs('test', Repo\Country::get(), Repo\City::get(), 'parent');

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Repo\Country::get(), $rel->getRepo());
        $this->assertSame(Repo\City::get(), $rel->getForeignRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('parentId', $rel->getForeignKey());
        $this->assertSame('parentClass', $rel->getForeignClassKey());

        $rel = new HasManyAs(
            'test',
            Repo\City::get(),
            Repo\Country::get(),
            'parent',
            ['foreignKey' => 'test', 'foreignClassKey' => 'testClass']
        );
        $this->assertSame('test', $rel->getForeignKey());
        $this->assertSame('testClass', $rel->getForeignClassKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new HasManyAs('users', Repo\City::get(), Repo\User::get(), 'location');

        $models = new Models([
            new Model\City(),
            new Model\City(),
        ]);

        $this->assertFalse($rel->hasForeign($models));

        $models = new Models([
            new Model\City(['id' => null]),
            new Model\City(['id' => 2]),
        ]);

        $this->assertTrue($rel->hasForeign($models));
    }

    /**
     * @covers ::loadForeign
     */
    public function testLoadForeign()
    {
        $rel = new HasManyAs('users', Repo\City::get(), Repo\User::get(), 'location');

        $models = new Models([
            new Model\City(['id' => 1]),
            new Model\City(['id' => 2]),
            new Model\City(['id' => 3]),
        ]);

        $users = $rel->loadForeign($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\Model\User', $users);
        $this->assertCount(2, $users);

        $this->assertEquals(1, $users[0]->id);
        $this->assertEquals(2, $users[1]->id);
    }

    public function dataAreLinked()
    {
        return [
            [
                new Model\City(['id' => 2]),
                new Model\User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\Model\City']),
                false,
            ],
            [
                new Model\City(['id' => 12]),
                new Model\User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\Model\Country']),
                false,
            ],
            [
                new Model\City(['id' => 12]),
                new Model\User(['locationId' => 12, 'locationClass' => 'Harp\Harp\Test\Model\City']),
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
        $rel = new HasManyAs('users', Repo\City::get(), Repo\User::get(), 'location');

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasManyAs('users', Repo\City::get(), Repo\User::get(), 'location');

        $model = new Model\City(['id' => 2]);
        $foreign1 = new Model\User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\Model\City']);
        $foreign2 = new Model\User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\Model\City']);
        $foreign3 = new Model\User(['locationId' => 8, 'locationClass' => 'Harp\Harp\Test\Model\Country']);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($link);

        $this->assertEquals(null, $foreign1->locationId);
        $this->assertEquals(null, $foreign1->locationClass);
        $this->assertEquals(2, $foreign2->locationId);
        $this->assertEquals('Harp\Harp\Test\Model\City', $foreign2->locationClass);
        $this->assertEquals(2, $foreign3->locationId);
        $this->assertEquals('Harp\Harp\Test\Model\City', $foreign3->locationClass);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasManyAs('users', Repo\City::get(), Repo\User::get(), 'location');

        $select = new Select(Repo\City::get());

        $rel->join($select, 'City');

        $this->assertEquals(
            'SELECT City.* FROM City JOIN User AS users ON users.locationId = City.id AND users.locationClass = "Harp\Harp\Test\Model\City" AND users.deletedAt IS NULL',
            $select->humanize()
        );
    }
}
