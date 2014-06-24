<?php

namespace Harp\Harp\Test\Unit;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Query\DB;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\AbstractModel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::where
     * @covers ::whereRaw
     * @covers ::whereLike
     * @covers ::whereNot
     * @covers ::whereIn
     */
    public function testFind()
    {
        $repo = $this->getMock('Harp\Harp\Test\Repo\User', ['findAll']);

        ModelMock::setRepoStatic($repo);

        $find = $this->getMock(
            'Harp\Harp\Find',
            [
                'where',
                'whereNot',
                'whereIn',
                'whereLike',
                'whereRaw'
            ],
            [$repo]
        );

        $repo
            ->expects($this->exactly(5))
            ->method('findAll')
            ->will($this->returnValue($find));

        $find
            ->expects($this->once())
            ->method('where')
            ->with($this->equalTo('test'), $this->equalTo('val'))
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('whereNot')
            ->with($this->equalTo('test'), $this->equalTo('val'))
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('whereLike')
            ->with($this->equalTo('test'), $this->equalTo('val'))
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('whereRaw')
            ->with($this->equalTo('query'), $this->equalTo(['val']))
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('whereIn')
            ->with($this->equalTo('test'), $this->equalTo(['val']))
            ->will($this->returnSelf());


        $this->assertSame($find, ModelMock::where('test', 'val'));
        $this->assertSame($find, ModelMock::whereNot('test', 'val'));
        $this->assertSame($find, ModelMock::whereLike('test', 'val'));
        $this->assertSame($find, ModelMock::whereRaw('query', ['val']));
        $this->assertSame($find, ModelMock::whereIn('test', ['val']));
    }

}
