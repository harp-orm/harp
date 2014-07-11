<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Model\DirtyTrackingTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class DirtyTrackingTraitTest extends AbstractTestCase
{
    /**
     * @covers ::setOriginals
     * @covers ::getOriginals
     * @covers ::getOriginal
     */
    public function testOriginals()
    {
        $object = new City();

        $originals = $object->getOriginals();
        $original = $object->getOriginal('name');

        $this->assertEquals(['id' => null, 'name' => null, 'countryId' => null], $originals);

        $expected = ['id' => 10, 'name' => 'test', 'countryId' => 1];

        $object->setOriginals($expected);

        $originals = $object->getOriginals();
        $original = $object->getOriginal('name');

        $this->assertEquals($expected, $originals);
        $this->assertEquals($expected['name'], $original);
    }

    /**
     * @covers ::hasChange
     */
    public function testHasChange()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $this->assertFalse($object->hasChange('name'));

        $object->name = 'new val';

        $this->assertFalse($object->hasChange('id'));
        $this->assertTrue($object->hasChange('name'));

        $object->name = 'test1';

        $this->assertFalse($object->hasChange('id'));
        $this->assertFalse($object->hasChange('name'));
    }

    /**
     * @covers ::getChange
     */
    public function testGetChange()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $this->assertNull($object->getChange('name'));

        $object->name = 'new val';

        $expected = ['test1', 'new val'];

        $this->assertNull($object->getChange('id'));
        $this->assertEquals($expected, $object->getChange('name'));

        $object->name = 'test1';

        $this->assertNull($object->getChange('id'));
        $this->assertNull($object->getChange('name'));
    }

    /**
     * @covers ::getChanges
     */
    public function testGetChanges()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $this->assertEmpty($object->getChanges());

        $object->name = 'new val';

        $expected = ['name' => 'new val'];

        $this->assertEquals($expected, $object->getChanges());

        $object->id = 20;

        $expected = [
            'name' => 'new val',
            'id' => 20,
        ];

        $this->assertEquals($expected, $object->getChanges());

        $object->name = 'test1';
        $object->id = 10;

        $this->assertEmpty($object->getChanges());
    }

    /**
     * @covers ::isEmptyChanges
     * @covers ::isChanged
     */
    public function testChanged()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $this->assertTrue($object->isEmptyChanges());
        $this->assertFalse($object->isChanged());

        $object->name = 'new val';

        $this->assertFalse($object->isEmptyChanges());
        $this->assertTrue($object->isChanged());

        $object->id = 20;

        $this->assertFalse($object->isEmptyChanges());
        $this->assertTrue($object->isChanged());

        $object->name = 'test1';
        $object->id = 10;

        $this->assertTrue($object->isEmptyChanges());
        $this->assertFalse($object->isChanged());
    }

    /**
     * @covers ::getProperties
     * @covers ::getPublicPropertiesOf
     */
    public function testGetProperties()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $properties = $object->getProperties();
        $expected = [
            'id' => 10,
            'name' => 'test1',
            'countryId' => null
        ];

        $this->assertEquals($expected, $properties);
    }

    /**
     * @covers ::setProperties
     */
    public function testSetProperties()
    {
        $object = new City(['id' => 10, 'name' => 'test1']);

        $object->setProperties([
            'id' => 20,
            'name' => 'changed',
        ]);

        $this->assertEquals(20, $object->id);
        $this->assertEquals('changed', $object->name);
    }

    /**
     * @covers ::resetOriginals
     */
    public function testResetOriginals()
    {
        $model = new City(['id' => 1, 'name' => 'test 2']);

        $expected = ['id' => 1, 'name' => 'test 2', 'countryId' => null];

        $model->name = 'test 3';
        $model->id = 4;

        $this->assertEquals($expected, $model->getOriginals());

        $model->resetOriginals();

        $expected = ['id' => 4, 'name' => 'test 3', 'countryId' => null];

        $this->assertEquals($expected, $model->getOriginals());
    }
}
