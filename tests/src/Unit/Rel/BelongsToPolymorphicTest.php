<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\Models;
use Harp\Harp\Rel\BelongsToPolymorphic;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\BelongsToPolymorphic
 */
class BelongsToPolymorphicTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getClassKey
     * @covers ::getForeignKey
     */
    public function testConstruct()
    {
        $rel = new BelongsToPolymorphic('test', Repo\User::get(), Repo\Country::get());

        $this->assertSame('test', $rel->getName());
        $this->assertSame(Repo\User::get(), $rel->getRepo());
        $this->assertSame(Repo\Country::get(), $rel->getForeignRepo());
        $this->assertSame('testId', $rel->getKey());
        $this->assertSame('testClass', $rel->getClassKey());
        $this->assertSame('id', $rel->getForeignKey());

        $rel = new BelongsToPolymorphic(
            'test',
            Repo\City::get(),
            Repo\Country::get(),
            ['key' => 'test', 'classKey' => 'testClass']
        );

        $this->assertSame('test', $rel->getKey());
        $this->assertSame('testClass', $rel->getClassKey());
    }

    /**
     * @covers ::hasForeign
     */
    public function testHasForeign()
    {
        $rel = new BelongsToPolymorphic('test', Repo\User::get(), Repo\Country::get());

        $models = new Models([
            new Model\City(),
            new Model\City(),
        ]);

        $this->assertFalse($rel->hasForeign($models));

        $models = new Models([
            new Model\City(['testId' => 1, 'testClass' => null]),
            new Model\City(['testId' => 2, 'testClass' => null]),
        ]);

        $this->assertFalse($rel->hasForeign($models));

        $models = new Models([
            new Model\City(['testId' => 1, 'testClass' => 'Harp\Harp\Test\Model\Country']),
            new Model\City(['testId' => 2, 'testClass' => 'Harp\Harp\Test\Model\City']),
        ]);

        $this->assertTrue($rel->hasForeign($models));

    }

    /**
     * @covers ::loadForeign
     */
    public function testLoadForeign()
    {
        $rel = new BelongsToPolymorphic('location', Repo\User::get(), Repo\Country::get());

        $models = new Models([
            new Model\User(['locationId' => null, 'locationClass' => null]),
            new Model\User(['locationId' => null, 'locationClass' => 'Harp\Harp\Test\Model\Post']),
            new Model\User(['locationId' => 1, 'locationClass' => 'Harp\Harp\Test\Model\Country']),
            new Model\User(['locationId' => 1, 'locationClass' => 'Harp\Harp\Test\Model\City']),
        ]);

        $locations = $rel->loadForeign($models);

        $this->assertCount(2, $locations);

        $this->assertEquals(1, $locations[0]->id);
        $this->assertInstanceOf('Harp\Harp\Test\Model\Country', $locations[0]);

        $this->assertEquals(1, $locations[1]->id);
        $this->assertInstanceOf('Harp\Harp\Test\Model\City', $locations[1]);
    }

    public function dataAreLinked()
    {
        return [
            [
                new Model\User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\Model\Country']),
                new Model\City(['id' => 2]),
                false,
            ],
            [
                new Model\User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\Model\Country']),
                new Model\Country(),
                false,
            ],
            [
                new Model\User(['locationId' => 2, 'locationClass' => 'Harp\Harp\Test\Model\Country']),
                new Model\Country(['id' => 2]),
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
        $rel = new BelongsToPolymorphic('location', Repo\User::get(), Repo\Country::get());

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new BelongsToPolymorphic('location', Repo\User::get(), Repo\Country::get());

        $model = new Model\User();
        $foreign = new Model\Country(['id' => 20]);
        $link = new LinkOne($rel, $foreign);

        $rel->update($model, $link);

        $this->assertEquals(20, $model->locationId);
        $this->assertEquals('Harp\Harp\Test\Model\Country', $model->locationClass);
    }

    /**
     * @covers ::join
     * @expectedException BadMethodCallException
     */
    public function testJoin()
    {
        $rel = new BelongsToPolymorphic('location', Repo\User::get(), Repo\Country::get());

        $select = new Select(Repo\User::get());

        $rel->join($select, 'City');
    }
}
