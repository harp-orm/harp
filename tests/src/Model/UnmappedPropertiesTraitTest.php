<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class UnmappedPropertiesTraitTest extends AbstractTestCase
{
    /**
     * @covers Harp\Harp\Model\UnmappedPropertiesTrait
     */
    public function testAll()
    {
        $object = new City();

        $this->assertEmpty($object->getUnmapped());
        $this->assertFalse(isset($object->test3));

        $object->name = 'val1';
        $object->id = 20;

        $this->assertEmpty($object->getUnmapped());
        $this->assertFalse(isset($object->test3));
        $this->assertFalse(isset($object->test4));

        $object->test3 = 'val3';
        $object->test4 = 'val4';

        $expected = [
            'test3' => 'val3',
            'test4' => 'val4',
        ];

        $this->assertEquals($expected, $object->getUnmapped());
        $this->assertEquals('val3', $object->test3);
        $this->assertEquals('val4', $object->test4);
        $this->assertTrue(isset($object->test4));
        $this->assertTrue(isset($object->test4));
    }
}
