<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Model\State;
use Harp\Harp\Config;

/**
 * @coversDefaultClass Harp\Harp\Model\RepoProxyTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoProxyTraitTest extends AbstractTestCase
{
    public function getMockForMethod($method, $arguments)
    {
        $repo = $this->getMock('Harp\Harp\Repo', ['getModelClass'], [new Config('Harp\Harp\Test\TestModel\City')]);

        $repo
            ->expects($this->once())
            ->method('getModelClass')
            ->will($this->returnValue('Harp\Harp\Test\Model\TestProxy'));

        return $repo;
    }

    public function dataMethods()
    {
        return [
            ['find', ['id', State::DELETED]],
            ['findByName', ['name', State::DELETED]],
            ['updateAll', []],
            ['deleteAll', []],
            ['selectAll', []],
            ['insertAll', []],
            ['findAll', []],
        ];
    }

    /**
     * @dataProvider dataMethods
     *
     * @covers ::find
     * @covers ::findByName
     * @covers ::updateAll
     * @covers ::deleteAll
     * @covers ::selectAll
     * @covers ::insertAll
     * @covers ::findAll
     */
    public function testMethods($method, $arguments)
    {
        $repo = $this->getMockForMethod($method, $arguments);

        $result = call_user_func_array([$repo, $method], $arguments);

        $this->assertSame($method, $result);
    }
}
