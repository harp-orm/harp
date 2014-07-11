<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\EventListeners;
use Harp\Harp\Repo\Event;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;

/**
 * @coversDefaultClass Harp\Harp\Repo\EventListeners
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class EventListenersTest extends AbstractTestCase
{
    /**
     * @covers ::dispatchEvent
     */
    public function testDispatchEvent()
    {
        $model = new City();

        $listeners = [
            Event::SAVE => [
                function ($model) {
                    $model->callbackSave = true;
                }
            ]
        ];

        EventListeners::dispatchEvent($listeners, $model, Event::SAVE);

        $this->assertTrue($model->callbackSave);
    }

    /**
     * @covers ::getBefore
     * @covers ::addBefore
     * @covers ::hasBeforeEvent
     * @covers ::dispatchBeforeEvent
     */
    public function testBefore()
    {
        $model = new City();
        $listeners = new EventListeners();

        $this->assertEmpty($listeners->getBefore());
        $this->assertFalse($listeners->hasBeforeEvent(Event::INSERT));

        $listeners->addBefore(Event::INSERT, function ($model) {
            $model->callbackCalled = true;
        });

        $this->assertTrue($listeners->hasBeforeEvent(Event::INSERT));

        $listeners->dispatchBeforeEvent($model, Event::INSERT);

        $this->assertTrue($model->callbackCalled);
    }

    /**
     * @covers ::getAfter
     * @covers ::addAfter
     * @covers ::hasAfterEvent
     * @covers ::dispatchAfterEvent
     */
    public function testAfter()
    {
        $model = new City();
        $listeners = new EventListeners();

        $this->assertEmpty($listeners->getAfter());
        $this->assertFalse($listeners->hasAfterEvent(Event::INSERT));

        $listeners->addAfter(Event::INSERT, function ($model) {
            $model->callbackCalled = true;
        });

        $this->assertTrue($listeners->hasAfterEvent(Event::INSERT));

        $listeners->dispatchAfterEvent($model, Event::INSERT);

        $this->assertTrue($model->callbackCalled);
    }
}
