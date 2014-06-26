<?php

namespace Harp\Harp\Test\Unit;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Query\DB;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\AbstractRepo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRepoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::beforeInitialize
     * @covers ::afterInitialize
     * @covers ::getTable
     * @covers ::getFields
     * @covers ::getName
     */
    public function testConstruct()
    {
        $repo = new Repo\City();

        $this->assertEquals('City', $repo->getTable());
        $this->assertEquals('City', $repo->getName());
        $this->assertEquals(['id', 'name', 'countryId'], $repo->getFields());
    }

    /**
     * @covers ::getTable
     * @covers ::setTable
     */
    public function testTable()
    {
        $repo = new Repo\Post();

        $this->assertEquals('Post', $repo->getTable());

        $repo->setTable('custom_table');

        $this->assertEquals('custom_table', $repo->getTable());
    }


    /**
     * @covers ::setRootRepo
     * @covers ::getRootRepo
     */
    public function testRootRepo()
    {
        $repo = new Repo\BlogPost();
        $newRoot = new Repo\Tag();
        $newRoot->setInherited(true);

        $this->assertEquals('Post', $repo->getTable());

        $repo->setRootRepo($newRoot);

        $this->assertEquals('Tag', $repo->getTable());
        $this->assertEquals($newRoot, $repo->getRootRepo());
    }

    /**
     * @covers ::getDb
     * @covers ::setDb
     * @covers ::getDbInstance
     */
    public function testDb()
    {
        $repo = new Repo\City();

        $this->assertEquals('default', $repo->getDb());

        $this->assertSame($repo->getDbInstance(), DB::get('default'));

        $repo->setDb('custom_Db');

        $this->assertEquals('custom_Db', $repo->getDb());
    }

    /**
     * @covers ::getFields
     * @covers ::setFields
     */
    public function testFields()
    {
        $repo = new Repo\Country();

        $this->assertEquals(['id', 'name'], $repo->getFields());

        $repo->setFields(['id']);

        $this->assertEquals(['id'], $repo->getFields());
    }

    /**
     * @covers ::getRel
     * @covers ::getRelOrError
     */
    public function testRelOrError()
    {
        $repo = Repo\City::get();

        $this->assertInstanceof('Harp\Harp\Rel\RelInterface', $repo->getRel('country'));
        $this->assertInstanceof('Harp\Harp\Rel\RelInterface', $repo->getRelOrError('country'));
    }

    public function dataGetters()
    {
        return [
            ['Harp\Harp\Find', 'findAll'],
            ['Harp\Harp\Query\Select', 'selectAll'],
            ['Harp\Harp\Query\Update', 'updateAll'],
            ['Harp\Harp\Query\Delete', 'deleteAll'],
            ['Harp\Harp\Query\Insert', 'insertAll'],
        ];
    }

    /**
     * @dataProvider dataGetters
     * @covers ::findAll
     * @covers ::selectAll
     * @covers ::updateAll
     * @covers ::deleteAll
     * @covers ::insertAll
     */
    public function testGetters($class, $method)
    {
        $repo = new Repo\City();

        $obj = $repo->$method();

        $this->assertInstanceof($class, $obj);
        $this->assertSame($repo, $obj->getRepo());
    }

    /**
     * @covers ::update
     */
    public function testUpdateOne()
    {
        $repo = $this->getMock('Harp\Harp\Test\Repo\Country', ['updateAll']);

        $update = $this->getMock(
            'Harp\Harp\Query\Update',
            ['set', 'execute', 'where'],
            [$repo]
        );

        $repo
            ->expects($this->once())
            ->method("updateAll")
            ->will($this->returnValue($update));

        $update
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo(['name' => 'test']))
            ->will($this->returnSelf());

        $update
            ->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id'), $this->equalTo(10))
            ->will($this->returnSelf());

        $update
            ->expects($this->once())
            ->method('execute');

        $model = new Model\Country(['id' => 10], State::SAVED);
        $model->name = 'test';
        $models = new Models([$model]);

        $repo->update($models);
    }

    /**
     * @covers ::update
     */
    public function testUpdateMany()
    {
        $repo = $this->getMock('Harp\Harp\Test\Repo\Country', ['updateAll']);

        $update = $this->getMock(
            'Harp\Harp\Query\Update',
            ['models', 'execute'],
            [$repo]
        );

        $repo
            ->expects($this->once())
            ->method('updateAll')
            ->will($this->returnValue($update));

        $models = new Models([new Model\Country(), new Model\Country()]);

        $update
            ->expects($this->once())
            ->method('models')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $update
            ->expects($this->once())
            ->method('execute');

        $repo->update($models);
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $repo = $this->getMock('Harp\Harp\Test\Repo\Country', ['deleteAll']);

        $delete = $this->getMock(
            'Harp\Harp\Query\Delete',
            ['models', 'execute'],
            [$repo]
        );

        $repo
            ->expects($this->once())
            ->method('deleteAll')
            ->will($this->returnValue($delete));

        $models = new Models([new Model\Country(), new Model\Country()]);

        $delete
            ->expects($this->once())
            ->method('models')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $delete
            ->expects($this->once())
            ->method('execute');

        $repo->delete($models);
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $repo = $this->getMock('Harp\Harp\Test\Repo\Country', ['insertAll']);

        $insert = $this->getMock(
            'Harp\Harp\Query\Insert',
            ['models', 'execute', 'getLastInsertId'],
            [$repo]
        );

        $repo
            ->expects($this->once())
            ->method('insertAll')
            ->will($this->returnValue($insert));

        $models = new Models([new Model\Country(), new Model\Country()]);

        $insert
            ->expects($this->once())
            ->method('models')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $insert
            ->expects($this->once())
            ->method('getLastInsertId')
            ->will($this->returnValue(12));

        $insert
            ->expects($this->once())
            ->method('execute');

        $repo->insert($models);

        $this->assertEquals(12, $models->getFirst()->id);
        $this->assertEquals(13, $models->getNext()->id);
    }
}