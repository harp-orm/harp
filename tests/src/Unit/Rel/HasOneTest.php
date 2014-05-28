<?php

namespace CL\Luna\Test\Unit\Rel;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\Models;
use CL\Luna\Rel\HasOne;
use CL\Luna\Query\Select;
use CL\Luna\Test\AbstractTestCase;

/**
 * @coversDefaultClass CL\Luna\Rel\HasOne
 */
class HasOneTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new HasOne('test', Repo\Country::get(), Repo\City::get());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Repo\Country::get(), $rel->getRepo());
        $this->assertSame(Repo\City::get(), $rel->getForeignRepo());
        $this->assertSame('id', $rel->getKey());
        $this->assertSame('countryId', $rel->getForeignKey());

        $rel = new HasOne('test', Repo\Country::get(), Repo\City::get(), array('foreignKey' => 'test'));
        $this->assertSame('test', $rel->getForeignKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new HasOne('city', Repo\Country::get(), Repo\City::get());

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
        $rel = new HasOne('city', Repo\Country::get(), Repo\City::get());

        $models = new Models([
            new Model\Country(['id' => 1]),
            new Model\Country(['id' => 2]),
        ]);

        $cities = $rel->loadForeign($models);

        $this->assertContainsOnlyInstancesOf('CL\Luna\Test\Model\City', $cities);
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
        $rel = new HasOne('city', Repo\Country::get(), Repo\City::get());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasOne('city', Repo\Country::get(), Repo\City::get());

        $model = new Model\Country(['id' => 20]);
        $old = new Model\City(['countryId' => 20]);
        $foreign = new Model\City(['countryId' => 2]);
        $link = new LinkOne($rel, $old);
        $link->set($foreign);

        $rel->update($model, $link);

        $this->assertEquals(20, $foreign->countryId);
        $this->assertNull($old->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasOne('city', Repo\Country::get(), Repo\City::get());

        $select = new Select(Repo\Country::get());

        $rel->join($select, 'Country');

        $this->assertEquals(
            'SELECT Country.* FROM Country JOIN City AS city ON city.countryId = Country.id',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $rel = new HasOne('user', Repo\Address::get(), Repo\User::get());

        $select = new Select(Repo\Address::get());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT Address.* FROM Address JOIN User AS user ON user.addressId = Address.id AND user.deletedAt IS NULL',
            $select->humanize()
        );
    }
}
