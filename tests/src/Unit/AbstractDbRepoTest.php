<?php

namespace CL\Luna\Test\Unit;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\Atlas\DB;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\Luna\AbstractDbRepo
 */
class AbstractDbRepoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTable
     * @covers ::getFields
     * @covers ::getName
     */
    public function testConstruct()
    {
        $repo = new Repo\User('CL\Luna\Test\Model\City');

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
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\BlogPost',
            ['initializeOnce'],
            ['CL\Luna\Test\Model\BlogPost']
        );

        $repo
            ->expects($this->atLeastOnce())
            ->method('initializeOnce');

        $this->assertEquals('BlogPost', $repo->getTable());

        $repo->setTable('custom_table');

        $this->assertEquals('custom_table', $repo->getTable());
    }

    /**
     * @covers ::getDb
     * @covers ::setDb
     * @covers ::getDbInstance
     */
    public function testDb()
    {
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\City',
            ['initializeOnce'],
            ['CL\Luna\Test\Model\City']
        );

        $repo
            ->expects($this->atLeastOnce())
            ->method('initializeOnce');

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
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\Country',
            ['initializeOnce'],
            ['CL\Luna\Test\Model\Country']
        );

        $repo
            ->expects($this->atLeastOnce())
            ->method('initializeOnce');

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

        $this->assertInstanceof('CL\Luna\Rel\DbRelInterface', $repo->getRel('country'));
        $this->assertInstanceof('CL\Luna\Rel\DbRelInterface', $repo->getRelOrError('country'));
    }

    public function dataGetters()
    {
        return [
            ['CL\Luna\Find', 'findAll'],
            ['CL\Luna\Query\Select', 'selectAll'],
            ['CL\Luna\Query\Update', 'updateAll'],
            ['CL\Luna\Query\Delete', 'deleteAll'],
            ['CL\Luna\Query\Insert', 'insertAll'],
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
        $repo = new Repo\User('CL\Luna\Test\Model\City');

        $obj = $repo->$method();

        $this->assertInstanceof($class, $obj);
        $this->assertSame($repo, $obj->getRepo());
    }

    /**
     * @covers ::update
     */
    public function testUpdateOne()
    {
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\Country',
            ['updateAll'],
            ['CL\Luna\Test\Model\Country']
        );

        $update = $this->getMock(
            'CL\Luna\Query\Update',
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
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\Country',
            ['updateAll'],
            ['CL\Luna\Test\Model\Country']
        );

        $update = $this->getMock(
            'CL\Luna\Query\Update',
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
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\Country',
            ['deleteAll'],
            ['CL\Luna\Test\Model\Country']
        );

        $delete = $this->getMock(
            'CL\Luna\Query\Delete',
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
        $repo = $this->getMock(
            'CL\Luna\Test\Repo\Country',
            ['insertAll'],
            ['CL\Luna\Test\Model\Country']
        );

        $insert = $this->getMock(
            'CL\Luna\Query\Insert',
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
