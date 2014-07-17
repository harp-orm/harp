<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;

/**
 * @coversDefaultClass Harp\Harp\Repo\LinkMany
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LinkManyTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::getRel
     * @covers ::getOriginal
     * @covers ::clear
     */
    public function testConstruct()
    {
        $rel = Country::getRepo()->getRel('cities');

        $models = [new City(), new City()];

        $link = new LinkMany(new Country(), $rel, $models);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($models, $link->get()->toArray());
        $this->assertSame($models, $link->getOriginal()->toArray());

        $link->clear();
        $this->assertCount(0, $link);
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $models = [new City()];

        $rel = $this->getMock(
            'Harp\Harp\Rel\HasManyThrough',
            ['delete'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo(), 'test']
        );

        $link = new LinkMany(new Country(), $rel, $models);
        $expected = new Models();

        $rel
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($expected));

        $result = $link->delete();
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::delete
     */
    public function testNoDelete()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\HasMany',
            ['delete'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo()]
        );

        $link = new LinkMany(new Country(), $rel, [new City()]);
        $models = new Models();

        $result = $link->delete();
        $this->assertNull($result);
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\HasManyThrough',
            ['insert'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo(), 'test']
        );

        $link = new LinkMany(new Country(), $rel, [new City()]);
        $expected = new Models();

        $rel
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($expected));

        $reuslt = $link->insert();

        $this->assertSame($expected, $reuslt);
    }

    /**
     * @covers ::insert
     */
    public function testNoInsert()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\HasMany',
            ['insert'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo()]
        );

        $link = new LinkMany(new Country(), $rel, [new City()]);
        $models = new Models();

        $result = $link->insert();
        $this->assertNull($result);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\HasMany',
            ['update'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo()]
        );

        $link = new LinkMany(new Country(), $rel, [new City()]);
        $expected = new Models();

        $rel
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($expected));

        $reuslt = $link->update();
        $this->assertSame($expected, $reuslt);

    }

    /**
     * @covers ::update
     */
    public function testNoUpdate()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\HasManyThrough',
            ['update'],
            ['cities', Country::getRepo()->getConfig(), City::getRepo(), 'test']
        );

        $link = new LinkMany(new Country(), $rel, [new City()]);
        $models = new Models();

        $result = $link->update();
        $this->assertNull($result);
    }

    /**
     * @covers ::addArray
     */
    public function testAddArray()
    {
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), []);

        $models = [new City(), new City()];

        $link->addArray($models);

        $this->assertSame($models, $link->toArray());
    }

    /**
     * @covers ::addModels
     */
    public function testAddModels()
    {
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), []);

        $models = new Models([new City(), new City()]);

        $link->addModels($models);

        $this->assertSame($models->toArray(), $link->toArray());
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), []);

        $model = new City();

        $link->add($model);

        $this->assertSame([$model], $link->toArray());
    }

    /**
     * @covers ::add
     */
    public function testAddWithInverse()
    {
        $user = new User();
        $post = new Post();

        $user->getPosts()->add($post);

        $this->assertSame($user, $post->getUser());
    }

    /**
     * @covers ::isChanged
     */
    public function testIsChanged()
    {
        $city = new City();
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), [$city]);

        $this->assertFalse($link->isChanged());


        $link->add($city);
        $this->assertFalse($link->isChanged());

        $newCity = new City();

        $link->add($newCity);
        $this->assertTrue($link->isChanged());
    }

    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $city = new City();
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), [$city]);

        $link->remove($city);

        $this->assertCount(0, $link);
        $this->assertFalse($link->has($city));

        $link->remove($city);

        $this->assertCount(0, $link);
        $this->assertFalse($link->has($city));
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmpty()
    {
        $city = new City();
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), [$city]);

        $this->assertFalse($link->isEmpty());

        $emptyLink = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), []);

        $this->assertTrue($emptyLink->isEmpty());
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $city = new City();
        $city2 = new City();
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), [$city]);

        $this->assertFalse($link->has($city2));
        $this->assertTrue($link->has($city));
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $array = $link->toArray();

        $this->assertSame($models, $array);
    }

    /**
     * @covers ::getOriginal
     */
    public function testGetOriginal()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $link->add(new City());

        $this->assertSame($models, $link->getOriginal()->toArray());
    }

    /**
     * @covers ::getAdded
     */
    public function testGetAdded()
    {
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), [new City]);
        $models = [new City(), new City()];

        $link->add($models[0]);
        $link->add($models[1]);

        $added = $link->getAdded();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $added);
        $this->assertSame($models, $added->toArray());
    }

    /**
     * @covers ::getRemoved
     */
    public function testGetRemoved()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $link
            ->add(new City())
            ->remove($models[0])
            ->remove($models[1]);

        $removed = $link->getRemoved();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $removed);
        $this->assertSame($models, $removed->toArray());
    }

    /**
     * @covers ::getCurrentAndOriginal
     */
    public function testGetCurrentAndOriginal()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $model1 = new City();
        $model2 = new City();

        $link
            ->add($model1)
            ->add($model2)
            ->remove($models[0]);

        $result = $link->getCurrentAndOriginal();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $result);

        $this->assertCount(4, $result);
        $this->assertTrue($result->has($model1));
        $this->assertTrue($result->has($model2));
        $this->assertTrue($result->has($models[0]));
        $this->assertTrue($result->has($models[1]));
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirst()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $this->assertSame($models[0], $link->getFirst());

        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), []);
        $first = $link->getFirst();

        $this->assertInstanceof('Harp\Harp\Test\TestModel\City', $first);
        $this->assertTrue($first->isVoid());
    }

    /**
     * @covers ::getNext
     */
    public function testGetNext()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);
        $link->getFirst();

        $this->assertSame($models[1], $link->getNext());

        $next = $link->getNext();

        $this->assertInstanceof('Harp\Harp\Test\TestModel\City', $next);
        $this->assertTrue($next->isVoid());
    }

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        $models = [new City(['id' => 10]), new City(['id' => 20])];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $result = $link->filter(function ($item) {
            return $item->id == 10;
        });

        $this->assertSame([$models[0]], $result->toArray());
    }

    /**
     * @covers ::sort
     */
    public function testSort()
    {
        $city1 = new City(['id' => 10]);
        $city2 = new City(['id' => 20]);

        $models = [$city2, $city1];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $result = $link->sort(function ($item1, $item2) {
            return $item1->id - $item2->id;
        });

        $this->assertSame([$city1, $city2], $result->toArray());
    }

    /**
     * @covers ::map
     */
    public function testMap()
    {
        $models = [new City(['id' => 10]), new City(['id' => 20])];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $result = $link->map(function ($item) {
            return $item->id;
        });

        $this->assertSame([10, 20], $result);
    }

    /**
     * @covers ::invoke
     */
    public function testInvoke()
    {
        $models = [new City(['id' => 10]), new City(['id' => 20])];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $result = $link->invoke('getId');

        $this->assertSame([10, 20], $result);
    }

    /**
     * @covers ::count
     */
    public function testCountable()
    {
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $this->assertCount(2, $link);
        $link->add(new City());
        $this->assertCount(3, $link);
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
        $models = [new City(), new City()];
        $link = new LinkMany(new Country(), Country::getRepo()->getRel('cities'), $models);

        $key = $link->key();

        foreach ($link as $i => $item) {
            $this->assertSame(current($models), $item);
            next($models);
        }
    }
}
