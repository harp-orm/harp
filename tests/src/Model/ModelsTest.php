<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Model\Models;
use Harp\Util\Objects;
use SplObjectStorage;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;

/**
 * @coversDefaultClass Harp\Harp\Model\Models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelsTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::all
     */
    public function testConstruct()
    {
        $source = [new City(), new City()];

        $models = new Models($source);

        $this->assertSame($source, Objects::toArray($models->all()));
    }

    /**
     * @covers ::assertValid
     */
    public function testAssertValid()
    {
        $source = [
            new City(['name' => 'test', 'other' => 'test2']),
            new City(['name' => 'test', 'other' => 'test2']),
        ];

        $models = new Models($source);

        $this->assertSame($models, $models->assertValid());

        $source[0]->name = null;

        $this->setExpectedException('Harp\Validate\InvalidException');

        $models->assertValid();
    }

    /**
     * @covers ::__clone
     */
    public function testClone()
    {
        $source = [new City(), new City()];
        $models = new Models($source);

        $clone = clone $models;

        $this->assertSame($source, $clone->toArray());
        $this->assertEquals($models->all(), $clone->all());
        $this->assertNotSame($models->all(), $clone->all());
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $models = new Models([new City(), new City()]);

        $this->assertCount(2, $models);

        $models->clear();

        $this->assertCount(0, $models);
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirst()
    {
        $model1 = new City();
        $model2 = new City();

        $models = new Models([$model1, $model2]);

        $this->assertSame($model1, $models->getFirst());

        $models->clear();

        $this->assertNull($models->getFirst());
    }

    /**
     * @covers ::getNext
     */
    public function testGetNext()
    {
        $model1 = new City();
        $model2 = new City();
        $model3 = new City();

        $models = new Models([$model1, $model2, $model3]);

        $models->getFirst();

        $this->assertSame($model2, $models->getNext());
        $this->assertSame($model3, $models->getNext());
        $this->assertNull($models->getNext());
    }

    /**
     * @covers ::addObjects
     */
    public function testAddObjects()
    {
        $models = new Models();
        $model1 = new City();
        $model2 = new City();

        $objects = new SplObjectStorage();
        $objects->attach($model1);
        $objects->attach($model2);

        $models->addObjects($objects);

        $this->assertSame([$model1, $model2], Objects::toArray($models->all()));
    }

    /**
     * @covers ::addArray
     */
    public function testAddArray()
    {
        $models = new Models();
        $array = [new City(), new City()];

        $models->addArray($array);

        $this->assertSame($array, Objects::toArray($models->all()));
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $models = new Models();

        $model = new City();

        $models->add($model);

        $this->assertSame([$model], Objects::toArray($models->all()));
    }

    /**
     * @covers ::addAll
     */
    public function testAddAll()
    {
        $models = new Models();

        $model1 = new City();
        $model2 = new City();
        $model3 = new City();

        $models->addAll(new Models());

        $this->assertEmpty($models);

        $models->addAll(new Models([$model1, $model2]));
        $models->addAll(new Models([$model1, $model3]));

        $this->assertCount(3, $models);

        $this->assertSame([$model1, $model2, $model3], Objects::toArray($models->all()));
    }


    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $model = new City();
        $models = new Models([$model]);

        $models->remove($model);

        $this->assertCount(0, $models);
    }

    /**
     * @covers ::removeAll
     */
    public function testRemoveAll()
    {
        $source1 = [new City(), new City()];
        $source2 = array_merge([new City()], $source1);
        $models1 = new Models($source1);
        $models2 = new Models($source2);

        $models2->removeAll($models1);

        $this->assertCount(1, $models2);
    }

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        $source = [
            new City(['name' => 'test1']),
            new City(['name' => 'test1']),
            new City(['name' => 'test2']),
        ];

        $models = new Models($source);

        $filtered = $models->filter(function($model){
            return $model->name !== 'test1';
        });

        $this->assertInstanceOf('Harp\Harp\Model\Models', $filtered);
        $this->assertEquals([$source[2]], Objects::toArray($filtered->all()));
    }

    /**
     * @covers ::sort
     */
    public function testSort()
    {
        $city1 = new City(['id' => 1]);
        $city2 = new City(['id' => 3]);
        $city3 = new City(['id' => 8]);

        $source = [$city1, $city3, $city2];

        $models = new Models($source);

        $sorted = $models->sort(function ($city1, $city2) {
            return $city1->id - $city2->id;
        });

        $this->assertInstanceOf('Harp\Harp\Model\Models', $sorted);
        $this->assertEquals([$city1, $city2, $city3], Objects::toArray($sorted->all()));
    }

    /**
     * @covers ::invoke
     */
    public function testInvoke()
    {
        $source = [
            new City(['id' => 1]),
            new City(['id' => 1]),
            new City(['id' => 2]),
        ];

        $models = new Models($source);

        $result = $models->invoke('getId');

        $this->assertEquals([1, 1, 2], $result);
    }


    /**
     * @covers ::map
     */
    public function testMap()
    {
        $source = [
            new City(['name' => 'test1']),
            new City(['name' => 'test1']),
            new City(['name' => 'test2']),
        ];

        $models = new Models($source);

        $result = $models->map(function ($model) {
            return $model->name;
        });

        $this->assertEquals(['test1', 'test1', 'test2'], $result);
    }
    /**
     * @covers ::byRepo
     */
    public function testByRepo()
    {
        $source = [
            0 => new City(),
            1 => new City(),
            2 => new Country(),
            3 => new City(),
            4 => new Country(),
        ];

        $models = new Models($source);

        $expected = [
            [City::getRepo(), [$source[0], $source[1], $source[3]]],
            [Country::getRepo(), [$source[2], $source[4]]],
        ];

        $i = 0;

        $models->byRepo(function($repo, Models $repoModels) use ($expected, & $i) {
            $this->assertSame($expected[$i][0], $repo);
            $this->assertSame($expected[$i][1], Objects::toArray($repoModels->all()));
            $i++;
        });
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmpty()
    {
        $model = new City();
        $models = new Models();

        $this->assertTrue($models->isEmpty());

        $models->add($model);

        $this->assertFalse($models->isEmpty());
    }

    /**
     * @covers ::hasId
     */
    public function testHasId()
    {
        $model = new City(['id' => 12]);
        $models = new Models();

        $this->assertFalse($models->hasId(12));

        $models->add($model);

        $this->assertTrue($models->hasId(12));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $model = new City();
        $models = new Models();

        $this->assertFalse($models->has($model));

        $models->add($model);

        $this->assertTrue($models->has($model));
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $source = [new City(), new City()];
        $models = new Models($source);

        $array = $models->toArray();

        $this->assertSame($source, $array);
    }

    /**
     * @covers ::count
     */
    public function testCountable()
    {
        $models = new Models([new City(), new City()]);
        $this->assertCount(2, $models);
        $models->add(new City());
        $this->assertCount(3, $models);
    }

    /**
     * @covers ::pluckProperty
     */
    public function testPluckProperty()
    {
        $models = new Models([
            new City(['id' => 10, 'name' => 'test1']),
            new City(['id' => 20, 'name' => 'test2']),
        ]);

        $expected = [10, 20];

        $this->assertSame($expected, $models->pluckProperty('id'));

        $expected = ['test1', 'test2'];

        $this->assertSame($expected, $models->pluckProperty('name'));
    }

    /**
     * @covers ::pluckPropertyUnique
     */
    public function testPluckPropertyUnique()
    {
        $models = new Models([
            new City(['id' => 10, 'name' => 'test1']),
            new City(['id' => 20, 'name' => 'test2']),
            new City(['id' => 10, 'name' => 'test2']),
            new City(['id' => null, 'name' => 'test2']),
        ]);

        $expected = [10, 20];

        $this->assertSame($expected, $models->pluckPropertyUnique('id'));

        $expected = ['test1', 'test2'];

        $this->assertSame($expected, $models->pluckPropertyUnique('name'));
    }

    /**
     * @covers ::getIds
     */
    public function testGetIds()
    {
        $models = new Models([
            new City(['id' => 10, 'name' => 'test1']),
            new City(['id' => 20, 'name' => 'test2']),
        ]);

        $expected = [10, 20];

        $this->assertSame($expected, $models->getIds());
    }

    /**
     * @covers ::isEmptyProperty
     */
    public function testIsEmptyProperty()
    {
        $models = new Models([
            new City(['id' => 10, 'name' => null]),
            new City(['id' => 20, 'name' => null]),
            new City(['id' => null, 'name' => null]),
        ]);

        $this->assertFalse($models->isEmptyProperty('id'));
        $this->assertTrue($models->isEmptyProperty('name'));
    }

    /**
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     */
    public function testIterator()
    {
        $source = [new City(), new City()];
        $models = new Models($source);

        $key = $models->key();

        foreach ($models as $i => $model) {
            $this->assertSame(current($source), $model);
            next($source);
        }
    }
}
