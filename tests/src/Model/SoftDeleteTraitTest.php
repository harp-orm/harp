<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Model\State;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Model\SoftDeleteTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SoftDeleteTraitTest extends AbstractTestCase
{
    /**
     * @covers ::delete
     * @covers ::initialize
     */
    public function testDelete()
    {
        $object = new User(null, State::SAVED);

        $this->assertNull($object->deletedAt);

        $object->delete();

        $this->assertNotNull($object->deletedAt);
        $this->assertEquals(State::DELETED, $object->getState());
    }

    /**
     * @covers ::getDefaultState
     */
    public function testGetDefaultState()
    {
        $object = new User();

        $this->assertEquals(State::PENDING, $object->getDefaultState());

        $object->deletedAt = time();

        $this->assertEquals(State::DELETED, $object->getDefaultState());
    }

    /**
     * @covers ::restore
     * @covers ::isSoftDeleted
     */
    public function testRestore()
    {
        $object = new User(null, State::SAVED);

        $object->delete();

        $this->assertTrue($object->isDeleted());
        $this->assertTrue($object->isSoftDeleted());

        $object->restore();

        $this->assertTrue($object->isSaved());
        $this->assertFalse($object->isSoftDeleted());
    }

    /**
     * @covers ::realDelete
     * @covers ::isSoftDeleted
     */
    public function testRealDelete()
    {
        $object = new User(null, State::SAVED);

        $object->delete();

        $this->assertTrue($object->isDeleted());
        $this->assertTrue($object->isSoftDeleted());

        $object->realDelete();

        $this->assertTrue($object->isDeleted());
        $this->assertFalse($object->isSoftDeleted());
    }
}
