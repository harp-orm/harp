<?php

namespace Harp\Harp\Test;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Model\State;

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
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $city = new City();

        $this->assertTrue($city->isPending());

        $city = new City([], State::DELETED);

        $this->assertTrue($city->isDeleted());

        $city = new City(['name' => 'test', 'id' => 3]);

        $this->assertEquals('test', $city->name);
        $this->assertEquals(3, $city->id);
    }

    /**
     * @covers ::hasSavedProperties
     */
    public function testHasSavedProperties()
    {
        $city = new City();

        $this->assertFalse($city->hasSavedProperties());

        $city = new City(['id' => 10]);

        $this->assertTrue($city->hasSavedProperties());
    }

    /**
     * @covers ::getValidationAsserts
     */
    public function testGetValidationAsserts()
    {
        $city = new City();

        $this->assertSame(City::getRepo()->getAsserts(), $city->getValidationAsserts());
    }

    /**
     * @covers ::isSoftDeleted
     */
    public function testIsSoftDeleted()
    {
        $city = new City();

        $this->assertFalse($city->isSoftDeleted());
    }

    /**
     * @covers ::getIdentityKey
     */
    public function testGetIdentityKey()
    {
        $city = new City();

        $this->assertNull($city->getIdentityKey());

        $city = new City(['id' => 10]);

        $this->assertNull($city->getIdentityKey());

        $city = new City(['id' => 10], State::SAVED);

        $this->assertEquals(10, $city->getIdentityKey());

    }
}
