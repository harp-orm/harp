<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.dirty_tracking
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class DirtyTrackingTest extends AbstractDbTestCase
{
    public function testTest()
    {
        $user = User::find(1);
        $name = $user->name;

        $this->assertFalse($user->isChanged());
        $this->assertEmpty($user->getChanges());

        $user->name = 'changed';
        $user->isBlocked = false;

        $this->assertTrue($user->isChanged());
        $this->assertTrue($user->hasChange('name'));
        $this->assertFalse($user->hasChange('isBlocked'));
        $this->assertEquals($name, $user->getOriginal('name'));
        $this->assertEquals(false, $user->getOriginal('isBlocked'));
        $this->assertEquals(['name' => 'changed'], $user->getChanges());

        $user->name = $name;
        $user->isBlocked = true;

        $this->assertTrue($user->isChanged());
        $this->assertFalse($user->hasChange('name'));
        $this->assertTrue($user->hasChange('isBlocked'));
        $this->assertEquals(['isBlocked' => true], $user->getChanges());

        $user->isBlocked = false;

        $this->assertFalse($user->isChanged());
        $this->assertEmpty($user->getChanges());
    }
}
