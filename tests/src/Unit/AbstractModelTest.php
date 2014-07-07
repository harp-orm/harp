<?php

namespace Harp\Harp\Test\Unit;

use Harp\Harp\Test\Model\User;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Query\DB;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\AbstractModel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractModelTest extends AbstractTestCase
{
    /**
     * @covers ::findAll
     */
    public function testFind()
    {
        $find = User::findAll();

        $this->assertInstanceOf('Harp\Harp\Find', $find);
        $this->assertSame(User::getRepo(), $find->getRepo());
    }

    /**
     * @covers ::newRepo
     */
    public function testNewRepo()
    {
        $repo = User::newRepo('Harp\Harp\Test\Model\User');

        $this->assertInstanceOf('Harp\Harp\Repo', $repo);
        $this->assertEquals('Harp\Harp\Test\Model\User', $repo->getModelClass());
        $this->assertTrue($repo->getSoftDelete());
    }

    /**
     * @covers ::where
     */
    public function testWhere()
    {
        $find = User::where('name', 'test');

        $this->assertEquals('SELECT `User`.* FROM `User` WHERE (`name` = "test")', $find->humanize());
    }

    /**
     * @covers ::whereRaw
     */
    public function testWhereRaw()
    {
        $find = User::whereRaw('name != ?', ['big']);

        $this->assertEquals('SELECT `User`.* FROM `User` WHERE (name != "big")', $find->humanize());
    }

    /**
     * @covers ::whereNot
     */
    public function testWhereNot()
    {
        $find = User::whereNot('name', 'test');

        $this->assertEquals('SELECT `User`.* FROM `User` WHERE (`name` != "test")', $find->humanize());
    }

    /**
     * @covers ::whereIn
     */
    public function testWhereIn()
    {
        $find = User::whereIn('name', ['test', 'test2']);

        $this->assertEquals('SELECT `User`.* FROM `User` WHERE (`name` IN ("test", "test2"))', $find->humanize());
    }

    /**
     * @covers ::whereLike
     */
    public function testWhereLike()
    {
        $find = User::whereLike('name', '%test');

        $this->assertEquals('SELECT `User`.* FROM `User` WHERE (`name` LIKE "%test")', $find->humanize());
    }
}
