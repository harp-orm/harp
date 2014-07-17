<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\RepoModels;
use Harp\Util\Objects;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;

/**
 * @coversDefaultClass Harp\Harp\Repo\RepoModels
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelsTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $source = [new City(), new City()];
        $repo = City::getRepo();

        $models = new RepoModels($repo, $source);

        $this->assertSame($repo, $models->getRepo());
        $this->assertSame($source, Objects::toArray($models->all()));
    }

    /**
     * @covers ::__clone
     */
    public function testClone()
    {
        $source = [new City(), new City()];
        $models = new RepoModels(City::getRepo(), $source);

        $clone = clone $models;

        $this->assertSame($source, $clone->toArray());
        $this->assertSame(City::getRepo(), $clone->getRepo());
        $this->assertEquals($models->all(), $clone->all());
        $this->assertNotSame($models->all(), $clone->all());
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirst()
    {
        $model1 = new City();
        $model2 = new City();

        $models = new RepoModels(City::getRepo(), [$model1, $model2]);

        $this->assertSame($model1, $models->getFirst());

        $models->clear();

        $first = $models->getFirst();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\City', $first);
        $this->assertTrue($first->isVoid());
    }

    /**
     * @covers ::getNext
     */
    public function testGetNext()
    {
        $model1 = new City();
        $model2 = new City();
        $model3 = new City();

        $models = new RepoModels(City::getRepo(), [$model1, $model2, $model3]);

        $models->getFirst();

        $this->assertSame($model2, $models->getNext());
        $this->assertSame($model3, $models->getNext());


        $next = $models->getNext();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\City', $next);
        $this->assertTrue($next->isVoid());
    }

    /**
     * @coversNothing
     */
    public function testFilter()
    {
        $source = [
            new City(['name' => 'test1']),
            new City(['name' => 'test1']),
            new City(['name' => 'test2']),
        ];

        $models = new RepoModels(City::getRepo(), $source);

        $filtered = $models->filter(function($model){
            return $model->name !== 'test1';
        });

        $this->assertInstanceOf('Harp\Harp\Repo\RepoModels', $filtered);
        $this->assertSame(City::getRepo(), $filtered->getRepo());
        $this->assertEquals([$source[2]], Objects::toArray($filtered->all()));
    }

    /**
     * @coversNothing
     */
    public function testSort()
    {
        $city1 = new City(['id' => 1]);
        $city2 = new City(['id' => 3]);
        $city3 = new City(['id' => 8]);

        $source = [$city1, $city3, $city2];

        $models = new RepoModels(City::getRepo(), $source);

        $sorted = $models->sort(function ($city1, $city2) {
            return $city1->id - $city2->id;
        });

        $this->assertInstanceOf('Harp\Harp\Repo\RepoModels', $sorted);
        $this->assertSame(City::getRepo(), $sorted->getRepo());
        $this->assertEquals([$city1, $city2, $city3], Objects::toArray($sorted->all()));
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $models = new RepoModels(City::getRepo());

        $model = new City();

        $models->add($model);

        $this->assertSame([$model], Objects::toArray($models->all()));
    }

    /**
     * @covers ::add
     * @expectedException InvalidArgumentException
     */
    public function testAddInvalid()
    {
        $models = new RepoModels(Country::getRepo());

        $models->add(new City());
    }
}
