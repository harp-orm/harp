<?php

namespace Harp\Db\Test\Unit;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Query\DB;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Db\AbstractDbRepo
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
        $repo = new Repo\User('Harp\Db\Test\Model\City');

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
            'Harp\Db\Test\Repo\BlogPost',
            ['initializeOnce'],
            ['Harp\Db\Test\Model\BlogPost']
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
            'Harp\Db\Test\Repo\City',
            ['initializeOnce'],
            ['Harp\Db\Test\Model\City']
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
            'Harp\Db\Test\Repo\Country',
            ['initializeOnce'],
            ['Harp\Db\Test\Model\Country']
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

        $this->assertInstanceof('Harp\Db\Rel\DbRelInterface', $repo->getRel('country'));
        $this->assertInstanceof('Harp\Db\Rel\DbRelInterface', $repo->getRelOrError('country'));
    }

    public function dataGetters()
    {
        return [
            ['Harp\Db\Find', 'findAll'],
            ['Harp\Db\Query\Select', 'selectAll'],
            ['Harp\Db\Query\Update', 'updateAll'],
            ['Harp\Db\Query\Delete', 'deleteAll'],
            ['Harp\Db\Query\Insert', 'insertAll'],
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
        $repo = new Repo\User('Harp\Db\Test\Model\City');

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
            'Harp\Db\Test\Repo\Country',
            ['updateAll'],
            ['Harp\Db\Test\Model\Country']
        );

        $update = $this->getMock(
            'Harp\Db\Query\Update',
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
            'Harp\Db\Test\Repo\Country',
            ['updateAll'],
            ['Harp\Db\Test\Model\Country']
        );

        $update = $this->getMock(
            'Harp\Db\Query\Update',
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
            'Harp\Db\Test\Repo\Country',
            ['deleteAll'],
            ['Harp\Db\Test\Model\Country']
        );

        $delete = $this->getMock(
            'Harp\Db\Query\Delete',
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
            'Harp\Db\Test\Repo\Country',
            ['insertAll'],
            ['Harp\Db\Test\Model\Country']
        );

        $insert = $this->getMock(
            'Harp\Db\Query\Insert',
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
