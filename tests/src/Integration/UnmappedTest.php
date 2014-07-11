<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.unmapped
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class UnmappedTest extends AbstractDbTestCase
{
    public function testTest()
    {
        $user = User::find(1);

        $this->assertEmpty($user->getUnmapped());

        $user->unmappedField = 'some value';
        $user->otherProp = 'val2';

        $this->assertEquals('some value', $user->unmappedField);

        $expected = [
            'unmappedField' => 'some value',
            'otherProp' => 'val2',
        ];

        $this->assertEquals($expected, $user->getUnmapped());
    }
}
