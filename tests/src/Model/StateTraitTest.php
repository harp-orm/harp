<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Model\State;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Model\StateTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class StateTraitTest extends AbstractTestCase
{
    public function dataSetStateNotVoid()
    {
        return [
            [
                ['id' => null],
                State::VOID,
                State::PENDING,
            ],
            [
                ['id' => 10],
                State::VOID,
                State::SAVED,
            ],
            [
                ['id' => null],
                State::SAVED,
                State::SAVED,
            ],
            [
                ['id' => 10],
                State::PENDING,
                State::PENDING,
            ],
        ];
    }

    /**
     * @dataProvider dataSetStateNotVoid
     * @covers ::setStateNotVoid
     */
    public function testSetStateNotVoid($parameters, $state, $expected)
    {
        $model = new City($parameters, $state);

        $model->setStateNotVoid();
        $this->assertEquals($expected, $model->getState());
    }

    /**
     * @covers ::getDefaultState
     */
    public function testGetDefaultState()
    {
        $model = new City();

        $model->setStateNotVoid();
        $this->assertEquals(State::PENDING, $model->getState());
    }

    /**
     * @covers ::getState
     */
    public function testGetState()
    {
        $model = new City();

        $model->getState(State::PENDING);

        $model = new City([], State::SAVED);

        $model->getState(State::SAVED);
    }

    /**
     * @covers ::setStateVoid
     * @covers ::isVoid
     */
    public function testStateVoid()
    {
        $model = new City();

        $this->assertFalse($model->isVoid());

        $model->setStateVoid();

        $this->assertEquals(State::VOID, $model->getState());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers ::isPending
     * @covers ::setState
     */
    public function testIsPending()
    {
        $model = new City(null, State::VOID);

        $this->assertFalse($model->isPending());
        $model->setState(State::PENDING);
        $this->assertTrue($model->isPending());
    }


    /**
     * @covers ::isSaved
     * @covers ::setState
     */
    public function testIsSaved()
    {
        $model = new City(null, State::VOID);

        $this->assertFalse($model->isSaved());
        $model->setState(State::SAVED);
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers ::isDeleted
     * @covers ::setState
     */
    public function testIsDeleted()
    {
        $model = new City(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->setState(State::DELETED);
        $this->assertTrue($model->isDeleted());
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $model = new City(null, State::SAVED);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertTrue($model->isDeleted());

        $model = new City(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertFalse($model->isDeleted(), 'Should not delete if void');
    }

    /**
     * @covers ::delete
     * @expectedException LogicException
     */
    public function testDeletePending()
    {
        $model = new City(null, State::PENDING);

        $model->delete();
    }

}
