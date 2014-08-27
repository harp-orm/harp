<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\Event;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @covers Harp\Harp\Repo\Event
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class EventTest extends AbstractTestCase
{
    public function testConstruct()
    {
        $events = [
            Event::CONSTRUCT,
            Event::INSERT,
            Event::UPDATE,
            Event::DELETE,
            Event::SAVE,
        ];

        $this->assertEquals(count($events), count(array_unique($events)), 'All events should be unique');
    }
}
