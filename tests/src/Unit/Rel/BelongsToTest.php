<?php

namespace Harp\Db\Test\Unit\Rel;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;
use Harp\Db\Rel\BelongsTo;
use Harp\Db\Query\Select;
use Harp\Db\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Db\Rel\BelongsTo
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
        $rel = new BelongsTo('test', Repo\City::get(), Repo\Country::get());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Repo\City::get(), $rel->getRepo());
        $this->assertSame(Repo\Country::get(), $rel->getForeignRepo());
        $this->assertSame('testId', $rel->getKey());
        $this->assertSame('id', $rel->getForeignKey());

        $rel = new BelongsTo('test', Repo\City::get(), Repo\Country::get(), array('key' => 'test'));
        $this->assertSame('test', $rel->getKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new BelongsTo('country', Repo\City::get(), Repo\Country::get());

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
        $rel = new BelongsTo('country', Repo\City::get(), Repo\Country::get());

        $models = new Models([
            new Model\City(['countryId' => null]),
            new Model\City(['countryId' => 1]),
            new Model\City(['countryId' => 2]),
        ]);

        $countries = $rel->loadForeign($models);

        $this->assertContainsOnlyInstancesOf('Harp\Db\Test\Model\Country', $countries);
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
        $rel = new BelongsTo('country', Repo\City::get(), Repo\Country::get());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new BelongsTo('country', Repo\City::get(), Repo\Country::get());

        $model = new Model\City(['countryId' => 2]);
        $foreign = new Model\Country(['id' => 20]);
        $link = new LinkOne($rel, $foreign);

        $rel->update($model, $link);

        $this->assertEquals(20, $model->countryId);
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new BelongsTo('country', Repo\City::get(), Repo\Country::get());

        $select = new Select(Repo\City::get());

        $rel->join($select, 'City');

        $this->assertEquals(
            'SELECT City.* FROM City JOIN Country AS country ON country.id = City.countryId',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $rel = new BelongsTo('user', Repo\Address::get(), Repo\User::get());

        $select = new Select(Repo\Address::get());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT Address.* FROM Address JOIN User AS user ON user.id = Address.userId AND user.deletedAt IS NULL',
            $select->humanize()
        );
    }
}
