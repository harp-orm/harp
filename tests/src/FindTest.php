<?php

namespace Harp\Harp\Test;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Model\State;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\RepoModels;
use Harp\Harp\Find;
use Harp\Harp\Config;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Find
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class FindTest extends AbstractDbTestCase
{
    public function getFindSelectTest($method, array $asserts = null, $return = null)
    {
        $select = $this->getMock(
            'Harp\Harp\Query\Select',
            [$method],
            [User::getRepo()]
        );

        $method = $select
            ->expects($this->once())
            ->method($method)
            ->will($return ?: $this->returnSelf());

        call_user_func_array([$method, 'with'], $asserts);

        $find = new Find(User::getRepo());
        $find->setSelect($select);

        return $find;
    }

    /**
     * @covers ::__construct
     * @covers ::getSelect
     * @covers ::setSelect
     * @covers ::getTable
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = User::getRepo();

        $find = new Find($repo);

        $this->assertSame($repo, $find->getRepo());
        $this->assertInstanceOf('Harp\Harp\Query\Select', $find->getSelect());
        $this->assertSame($repo, $find->getSelect()->getRepo());
        $this->assertSame('User', $find->getTable());

        $select = new Select($repo);

        $find->setSelect($select);

        $this->assertSame($select, $find->getSelect());
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
        $find->getRepo()->getConfig()->setSoftDelete(true);
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

    /**
     * @covers ::whereKeys
     */
    public function testWhereKeys()
    {
        $find = $this->getFindSelectTest(
            'whereIn',
            [$this->equalTo('User.id'), $this->equalTo([12, 3])]
        );

        $find->whereKeys([12, 3]);
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
        $find = new Find(Country::getRepo());

        $models = $find->execute();
        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\Country', $models);
        $this->assertCount(2, $models);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteInherited()
    {
        $find = new Find(Post::getRepo());

        $models = $find->execute();
        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\Post', $models);
        $this->assertCount(4, $models);

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\BlogPost', $models[3]);
    }

    /**
     * @covers ::loadRaw
     */
    public function testLoadRaw()
    {
        $repo = Country::getRepo();

        $models = [new Country(), new Country()];

        $find = $this->getMock('Harp\Harp\Find', ['execute', 'applyFlags'], [$repo]);

        $find
            ->expects($this->once())
            ->method('applyFlags')
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($models));

        $result = $find->loadRaw();

        $this->assertSame($models, $result);
    }

    /**
     * @covers ::setFlags
     * @covers ::getFlags
     */
    public function testFlags()
    {
        $repo = User::getRepo();

        $find = new Find($repo);

        $this->assertSame(State::SAVED, $find->getFlags());

        $find->setFlags(State::DELETED);

        $this->assertSame(State::DELETED, $find->getFlags());

        $find->setFlags(State::DELETED | State::SAVED);

        $this->assertSame(State::DELETED | State::SAVED, $find->getFlags());

        $this->setExpectedException('InvalidArgumentException', 'Flags were 1, but need to be State::SAVED, State::DELETED or State::DELETED | State::SAVED');

        $find->setFlags(State::PENDING);
    }


    /**
     * @covers ::onlyDeleted
     * @covers ::onlySaved
     * @covers ::deletedAndSaved
     */
    public function testFlagSetters()
    {
        $repo = User::getRepo();

        $find = new Find($repo);

        $find->onlyDeleted();

        $this->assertSame(State::DELETED, $find->getFlags());

        $find->onlySaved();

        $this->assertSame(State::SAVED, $find->getFlags());

        $find->deletedAndSaved();

        $this->assertSame(State::DELETED | State::SAVED, $find->getFlags());
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $repo = Country::getRepo();

        $model1 = new Country(['id' => 10], State::SAVED);
        $model2 = new Country(['id' => 10], State::SAVED);
        $model3 = new Country(['id' => 4], State::SAVED);
        $model4 = new Country(['id' => 4], State::SAVED);

        $find = $this->getMock('Harp\Harp\Find', ['loadRaw'], [$repo]);

        $find
            ->expects($this->exactly(2))
            ->method('loadRaw')
            ->will($this->onConsecutiveCalls([$model1, $model3], [$model2, $model4]));

        $loaded = $find->load();
        $this->assertInstanceOf('Harp\Harp\Model\Models', $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());

        $loaded = $find->load();
        $this->assertInstanceOf('Harp\Harp\Model\Models', $loaded);

        $this->assertSame([$model1, $model3], $loaded->toArray());
    }

    /**
     * @covers ::loadWith
     */
    public function testLoadWith()
    {
        $repo = $this->getMock('Harp\Harp\Repo', ['loadAllRelsFor'], [new Config('Harp\Harp\Test\TestModel\Country')]);
        $find = $this->getMock('Harp\Harp\Find', ['load'], [$repo]);

        $rels = ['one' => 'many'];

        $models = new Models([new Country()]);

        $find
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($models));

        $repo
            ->expects($this->once())
            ->method('loadAllRelsFor')
            ->with($this->identicalTo($models), $this->equalTo($rels), $this->equalTo(State::DELETED));

        $result = $find->setFlags(State::DELETED)->loadWith($rels);

        $this->assertSame($models, $result);
    }

    /**
     * @covers ::loadIds
     */
    public function testLoadIds()
    {
        $find = $this->getMock('Harp\Harp\Find', ['applyFlags'], [Post::getRepo()]);

        $find
            ->expects($this->once())
            ->method('applyFlags')
            ->with($this->equalTo(State::DELETED));

        $ids = $find->loadIds(State::DELETED);

        $expected = [1, 2, 3, 4];
        $this->assertEquals($expected, $ids);
    }

    /**
     * @covers ::loadFirst
     */
    public function testLoadFirst()
    {
        $repo = Country::getRepo();
        $find = $this->getMock('Harp\Harp\Find', ['limit', 'load'], [$repo]);

        $model = new Country(['id' => 300]);
        $models = new RepoModels($repo, [$model]);
        $emptyModels = new RepoModels($repo);

        $find
            ->expects($this->exactly(2))
            ->method('limit')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());

        $find
            ->expects($this->exactly(2))
            ->method('load')
            ->will($this->onConsecutiveCalls($models, $emptyModels));

        $result = $find->loadFirst();

        $this->assertSame($model, $result);

        $result = $find->loadFirst();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\Country', $result);
        $this->assertTrue($result->isVoid());
    }

    /**
     * @covers ::loadCount
     */
    public function testLoadCount()
    {
        $find = $this->getMock('Harp\Harp\Find', ['applyFlags'], [Post::getRepo()]);

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
