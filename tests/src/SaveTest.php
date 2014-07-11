<?php

namespace Harp\Harp\Test;

use Harp\Harp\Save;
use Harp\Harp\Repo\Event;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Repo\AbstractLink;
use Harp\Harp\Repo\Container;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Model\State;
use Harp\Harp\Model\Models;

/**
 * @coversDefaultClass Harp\Harp\Save
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SaveTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $model1 = new City();
        $model2 = new City();
        $save = new Save([$model1, $model2]);

        $this->assertCount(2, $save);
        $this->assertTrue($save->has($model1));
        $this->assertTrue($save->has($model2));
    }

    /**
     * @covers ::addShallow
     */
    public function testAddShallow()
    {
        $save = new Save();
        $model = new City();

        $save->addShallow($model);

        $this->assertCount(1, $save);
        $this->assertTrue($save->has($model));
    }

    public function dataFilters()
    {
        $models = [
            1 => (new City(null, State::SAVED))->setProperties(['name' => '1233']),
            2 => new City(null, State::VOID),
            3 => new City(null, State::PENDING),
            4 => new City(null, State::DELETED),
            5 => (new User(null, State::SAVED))->delete(),
            6 => new User(null, State::SAVED),
            7 => new User(null, State::PENDING),
            8 => new User(null, State::DELETED),
        ];

        return [
            [$models, 'getModelsToDelete', [$models[4], $models[8]]],
            [$models, 'getModelsToInsert', [$models[3], $models[7]]],
            [$models, 'getModelsToUpdate', [$models[1], $models[5]]],
        ];
    }

    /**
     * @dataProvider dataFilters
     * @covers ::getModelsToDelete
     * @covers ::getModelsToInsert
     * @covers ::getModelsToUpdate
     */
    public function testFilters($models, $filter, $expected)
    {
        $save = new Save();

        $save->addArray($models);

        $filtered = $save->$filter();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $filtered);
        $this->assertSame($expected, $filtered->toArray());
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $save = new Save();

        $models = [
            0 => new User(),
            1 => new Address(),
            2 => new Country(),
            3 => new City(),
            4 => new City(),
            5 => new City(),
            6 => new Address(),
        ];

        $link1 = new LinkOne($models[0], User::getRepo()->getRel('address'), $models[1]);
        $link1->set($models[6]);
        $link2 = new LinkMany($models[2], Country::getRepo()->getRel('cities'), [$models[3], $models[4]]);
        $link2->remove($models[3]);
        $link2->add($models[5]);

        User::getRepo()->addLink($link1);
        Country::getRepo()->addLink($link2);

        $save
            ->add($models[0])
            ->add($models[2]);

        $this->assertCount(count($models), $save);

        foreach ($models as $model) {
            $this->assertTrue($save->has($model));
        }
    }

    /**
     * @covers ::addArray
     */
    public function testAddArray()
    {
        $save = $this->getMock('Harp\Harp\Save', ['add']);

        $model1 = new City();
        $model2 = new City();

        $save
            ->expects($this->at(0))
            ->method('add')
            ->with($this->identicalTo($model1));

        $save
            ->expects($this->at(1))
            ->method('add')
            ->with($this->identicalTo($model2));

        $save->addArray([$model1, $model2]);
    }

    /**
     * @covers ::addAll
     */
    public function testAddAll()
    {
        $save = $this->getMock('Harp\Harp\Save', ['add']);

        $model1 = new City();
        $model2 = new City();

        $save
            ->expects($this->at(0))
            ->method('add')
            ->with($this->identicalTo($model1));

        $save
            ->expects($this->at(1))
            ->method('add')
            ->with($this->identicalTo($model2));

        $save->addAll(new Models([$model1, $model2]));
    }

    /**
     * @covers ::has
     * @covers ::count
     * @covers ::clear
     */
    public function testInterface()
    {
        $save = new Save();
        $model = new City();

        $this->assertFalse($save->has($model));
        $save->add($model);
        $this->assertTrue($save->has($model));

        $this->assertCount(1, $save);
        $save->clear();
        $this->assertCount(0, $save);
    }

    /**
     * @covers ::eachLink
     */
    public function testEachLink()
    {
        $save = new Save();

        $model1 = new User();
        $model2 = new Country();
        $model3 = new City();

        $link1 = new LinkOne($model1, User::getRepo()->getRel('location'), $model2);
        $link2 = new LinkMany($model2, Country::getRepo()->getRel('cities'), []);

        User::getRepo()->addLink($link1);
        Country::getRepo()->addLink($link2);

        $save->add($model1);

        $i = 0;

        $expected = [
            [$model1, $link1],
            [$model2, $link2]
        ];

        $save->eachLink(function(AbstractLink $link) use ($expected, $model3, & $i) {
            $this->assertSame($expected[$i][0], $link->getModel());
            $this->assertSame($expected[$i][1], $link);
            $i++;

            return new Models([$model3]);
        });

        $this->assertTrue($save->has($model3));
    }

    public function dataRelModifiers()
    {
        return [
            ['delete', 'addFromDeleteRels'],
            ['insert', 'addFromInsertRels'],
            ['update', 'addFromUpdateRels'],
        ];
    }

    /**
     * @dataProvider dataRelModifiers
     * @covers ::addFromDeleteRels
     * @covers ::addFromInsertRels
     * @covers ::addFromUpdateRels
     */
    public function testRelModifiers($method, $trigger)
    {
        $save = new Save();

        $model1 = new City();
        $model2 = new City();
        $model3 = new Country();
        $model4 = new Country();
        $model5 = new City();

        $rel = $this->getMock(
            __NAMESPACE__.'\TestBelongsTo',
            [$method],
            ['country', City::getRepo()->getConfig(), Country::getRepo()]
        );

        City::getRepo()->getConfig()->addRel($rel);

        $link1 = new LinkOne($model1, $rel, $model3);
        $link2 = new LinkOne($model2, $rel, $model4);

        City::getRepo()
            ->addLink($link1)
            ->addLink($link2);

        $save
            ->add($model1)
            ->add($model2);

        $rel
            ->expects($this->at(0))
            ->method($method)
            ->with($this->identicalTo($link1))
            ->will($this->returnValue(new Models([$model5])));

        $rel
            ->expects($this->at(1))
            ->method($method)
            ->with($this->identicalTo($link2))
            ->will($this->returnValue(null));

        $this->assertFalse($save->has($model5));

        $save->$trigger();

        $this->assertTrue($save->has($model5));
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $save = new Save();

        $repo1 = $this->getMock(
            'Harp\Harp\Repo',
            ['deleteModels', 'insertModels', 'updateModels', 'get'],
            [__NAMESPACE__.'\TestModel\Country']
        );

        $repo1
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($repo1));

        $repo2 = $this->getMock(
            'Harp\Harp\Repo',
            ['deleteModels', 'insertModels', 'updateModels', 'get'],
            [__NAMESPACE__.'\TestModel\User']
        );

        $repo2
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($repo2));

        Container::set(__NAMESPACE__.'\TestModel\Country', $repo1);
        Container::set(__NAMESPACE__.'\TestModel\User', $repo2);

        $models = [
            1 => (new Country(['id' => 1], State::SAVED))->setProperties(['name' => 'changed']),
            2 => new Country(['id' => 2], State::VOID),
            3 => new Country(['id' => 3], State::PENDING),
            4 => new Country(['id' => 4], State::DELETED),
            5 => (new User(['id' => 5, 'name' => 'test'], State::DELETED))->setProperties(['deletedAt' => time()]),
            6 => new User(['id' => 6, 'name' => 'test'], State::SAVED),
            7 => new User(['id' => 7, 'name' => 'test'], State::PENDING),
            8 => new User(['id' => 8, 'name' => 'test'], State::DELETED),
            9 => (new User(['id' => 9, 'name' => 'test'], State::SAVED))->setProperties(['name' => 'changed']),
        ];

        $save = new Save();
        $save->addArray($models);

        $expected = [
            'deleteModels' => [$models[4]],
            'insertModels' => [$models[3]],
            'updateModels' => [$models[1]],
        ];

        foreach ($expected as $method => $values) {
            $repo1
                ->expects($this->once())
                ->method($method)
                ->with($this->callback(function(Models $models) use ($values) {
                    $this->assertSame($values, $models->toArray());

                    return true;
                }));
        }

        $expected = [
            'deleteModels' => [$models[8]],
            'insertModels' => [$models[7]],
            'updateModels' => [$models[5], $models[9]],
        ];

        foreach ($expected as $method => $values) {
            $repo2
                ->expects($this->once())
                ->method($method)
                ->with($this->callback(function(Models $models) use ($values) {
                    $this->assertSame($values, $models->toArray());

                    return true;
                }));
        }

        $save->execute();
    }
}
