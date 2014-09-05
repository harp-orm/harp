<?php

namespace Harp\Harp\Test;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Repo;

/**
 * @coversDefaultClass Harp\Harp\ConfigProxyTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ConfigProxyTraitTest extends AbstractTestCase
{
    public function getMockForMethod($method, $arguments, $return)
    {
        $config = $this->getMock('Harp\Harp\Config', [$method], ['Harp\Harp\Test\TestModel\City'], '', false);

        $methodMock = $config
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue($return));

        if ($arguments) {
            $argumentConstraints = array_map(function ($argument) {
                return $this->equalTo($argument);
            }, $arguments);

            call_user_func_array([$methodMock, 'with'], $argumentConstraints);
        }

        return new Repo($config);
    }

    public function dataMethods()
    {
        return [
            ['getName', [], 'test'],
            ['getModelClass', [], 'test'],
            ['getTable', [], 'test'],
            ['getDb', [], 'test'],
            ['getReflectionModel', [], 'test'],
            ['getRootConfig', [], 'test'],
            ['getSoftDelete', [], 'test'],
            ['getInherited', [], 'test'],
            ['getPrimaryKey', [], 'test'],
            ['getNameKey', [], 'test'],
            ['getRels', [], 'test'],
            ['getFields', [], 'test'],
            ['getAsserts', [], 'test'],
            ['getSerializers', [], 'test'],
            ['getEventListeners', [], 'test'],
            ['getRel', ['testRelName'], 'test'],
            ['getRelOrError', ['testRelName'], 'test'],
            ['isModel', [new City()], true],
            ['assertModel', [new City()], true],
        ];
    }

    /**
     * @dataProvider dataMethods
     *
     * @covers ::getName
     * @covers ::getModelClass
     * @covers ::getTable
     * @covers ::getDb
     * @covers ::getReflectionModel
     * @covers ::getRootConfig
     * @covers ::getSoftDelete
     * @covers ::getInherited
     * @covers ::getPrimaryKey
     * @covers ::getNameKey
     * @covers ::getRels
     * @covers ::getFields
     * @covers ::getAsserts
     * @covers ::getSerializers
     * @covers ::getEventListeners
     * @covers ::getRel
     * @covers ::getRelOrError
     * @covers ::isModel
     * @covers ::assertModel
     */
    public function testMethods($method, $arguments, $expected)
    {
        $repo = $this->getMockForMethod($method, $arguments, $expected);

        $result = call_user_func_array([$repo, $method], $arguments);

        $this->assertSame($expected, $result);
    }
}
