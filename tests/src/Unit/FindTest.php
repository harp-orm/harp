<?php

namespace Harp\Harp\Test\Unit;

use Harp\Harp\Test\Model;
use Harp\Core\Model\State;
use Harp\Harp\Find;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Find
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class FindTest extends AbstractTestCase
{
    public function getFindSelectTest($method, array $asserts = null, $return = null)
    {
        $select = $this->getMock(
            'Harp\Harp\Query\Select',
            [$method],
            [Model\User::getRepo()]
        );

        $method = $select
            ->expects($this->once())
            ->method($method)
            ->will($return ?: $this->returnSelf());

        call_user_func_array([$method, 'with'], $asserts);

        $find = new Find(Model\User::getRepo());
        $find->setSelect($select);

        return $find;
    }

    /**
     * @covers ::__construct
     * @covers ::getSelect
     * @covers ::setSelect
     * @covers ::getTable
     */
    public function testConstruct()
    {
        $repo = Model\User::getRepo();

        $find = new Find($repo);

        $this->assertSame($repo, $find->getRepo());
        $this->assertInstanceOf('Harp\Harp\Query\Select', $find->getSelect());
        $this->assertSame($repo, $find->getSelect()->getRepo());
        $this->assertSame('User', $find->getTable());

        $select = new Select($repo);

        $find->setSelect($select);

        $this->assertSame($select, $find->getSelect());
    }

    public function dataSelectSetters()
    {
        return [
            ['column', ['name', 'alias']],
            ['column', ['name']],
            ['prependColumn', ['name', 'alias']],
            ['prependColumn', ['name']],
            ['where', ['name', 'val']],
            ['whereNot', ['name', 'val2']],
            ['whereRaw', ['test = ?', ['val2']]],
            ['whereIn', ['name', ['arr1', 'arr2']]],
            ['whereLike', ['name', 'val3']],
            ['having', ['name' ,'val']],
            ['havingNot', ['name', 'val']],
            ['havingIn', ['name', ['arr1', 'arr2']]],
            ['havingLike', ['name', 'val3']],
            ['group', ['name']],
            ['group', ['name', 'DESC']],
            ['order', ['name', 'ASC']],
            ['join', ['table', 'ON clause']],
            ['join', ['table', 'ON clause', 'type']],
            ['joinAliased', ['table', 'alias', ['col' => 'col']]],
            ['joinAliased', ['table', 'alias', ['col' => 'col'], 'type']],
            ['joinRels', [['test']]],
            ['limit', [12]],
            ['offset', [23]],
        ];
    }

    /**
     * @covers ::column
     * @covers ::prependColumn
     * @covers ::where
     * @covers ::whereNot
     * @covers ::whereIn
     * @covers ::whereRaw
     * @covers ::whereLike
     * @covers ::having
     * @covers ::havingNot
     * @covers ::havingIn
     * @covers ::havingLike
     * @covers ::group
     * @covers ::order
     * @covers ::join
     * @covers ::joinAliased
     * @covers ::joinRels
     * @covers ::limit
     * @covers ::offset
     * @dataProvider dataSelectSetters
     */
    public function testSelectSetters($methodName, $arguments)
    {
        $argumentAsserts = [];

        foreach ($arguments as $value) {
            $argumentAsserts []= $this->equalTo($value);
        }

        $find = $this->getFindSelectTest($methodName, $argumentAsserts);

        call_user_func_array([$find, $methodName], $arguments);
    }

    public function dataClearInterface()
    {
        return [
            ['clearColumns'],
            ['clearWhere'],
            ['clearHaving'],
            ['clearGroup'],
            ['clearOrder'],
            ['clearJoin'],
            ['clearLimit'],
            ['clearOffset'],
        ];
    }

    /**
     * @covers ::clearColumns
     * @covers ::clearWhere
     * @covers ::clearHaving
     * @covers ::clearGroup
     * @covers ::clearOrder
     * @covers ::clearJoin
     * @covers ::clearLimit
     * @covers ::clearOffset
     * @dataProvider dataClearInterface
     */
    public function testClearInterface($methodName)
    {
        $find = $this->getFindSelectTest($methodName, []);

        $find->$methodName();
    }

    public function dataFilters()
    {
        return [
            [State::SAVED, 'where', [$this->equalTo('User.deletedAt'), $this->equalTo(null)]],
            [State::DELETED, 'whereNot', [$this->equalTo('User.deletedAt'), $this->equalTo(null)]],
        ];
    }

    /**
     * @covers ::applyFlags
     * @dataProvider dataFilters
     */
    public function testFilters($flags, $expectedMethod, $expectedArguments)
    {
        $find = $this->getFindSelectTest($expectedMethod, $expectedArguments);
        $find->getRepo()->setSoftDelete(true);
        $find->setFlags($flags);

        $find->applyFlags();
    }

    /**
     * @covers ::whereKey
     */
    public function testWhereKey()
    {
        $find = $this->getFindSelectTest(
            'where',
            [$this->equalTo('User.id'), $this->equalTo(123)]
        );

        $find->whereKey(123);
    }

    public function dataStringRepresentations()
    {
        return [
            ['sql'],
            ['humanize'],
        ];
    }

    /**
     * @covers ::humanize
     * @covers ::sql
     * @dataProvider dataStringRepresentations
     */
    public function testStringRepresentations($method)
    {
        $find = $this->getFindSelectTest($method, [], $this->returnValue('test'));

        $result = $find->$method();

        $this->assertSame('test', $result);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteNormal()
    {
        $find = new Find(Model\Country::getRepo());

        $models = $find->execute();
        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\Model\Country', $models);
        $this->assertCount(2, $models);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteInherited()
    {
        $find = new Find(Model\Post::getRepo());

        $models = $find->execute();
        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\Model\Post', $models);
        $this->assertCount(4, $models);

        $this->assertInstanceOf('Harp\Harp\Test\Model\BlogPost', $models[3]);
    }

    /**
     * @covers ::loadIds
     */
    public function testLoadIds()
    {
        $find = $this->getMock('Harp\Harp\Find', ['applyFlags'], [Model\Post::getRepo()]);

        $find
            ->expects($this->once())
            ->method('applyFlags')
            ->with($this->equalTo(State::DELETED));

        $ids = $find->loadIds(State::DELETED);

        $expected = [1, 2, 3, 4];
        $this->assertEquals($expected, $ids);
    }

    /**
     * @covers ::loadCount
     */
    public function testLoadCount()
    {
        $find = $this->getMock('Harp\Harp\Find', ['applyFlags'], [Model\Post::getRepo()]);

        $find
            ->expects($this->once())
            ->method('applyFlags')
            ->with($this->equalTo(State::DELETED));

        $count = $find->loadCount(State::DELETED);

        $this->assertEquals(4, $count);

        $this->assertQueries([
            'SELECT COUNT(`Post`.`id`) AS `countAll` FROM `Post`'
        ]);
    }
}
