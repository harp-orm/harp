<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.void_models
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class VoidModelsTest extends AbstractDbTestCase
{
    public function testRels()
    {
        $user = User::find(1231421);

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\User', $user);
        $this->assertTrue($user->isVoid());

        $address = $user->getAddress();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\Address', $address);
        $this->assertTrue($address->isVoid());

        $post = $user->getPosts()->getFirst();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\Post', $post);
        $this->assertTrue($post->isVoid());

        $user = $post->getUser();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\User', $user);
        $this->assertTrue($user->isVoid());
    }
}
