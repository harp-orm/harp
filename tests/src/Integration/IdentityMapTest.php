<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.identity_map
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class IdentityMapTest extends AbstractDbTestCase
{
    public function testTest()
    {
        $user1 = User::find(1);

        $address1 = $user1->getAddress();

        $post1 = $user1->getPosts()->getFirst();

        $user2 = User::find(1);

        $address2 = $user2->getAddress();

        $post2 = $user2->getPosts()->getFirst();

        $address3 = Address::find(1);
        $post3 = Post::find(1);

        $this->assertSame($user1, $user2);
        $this->assertSame($address1, $address2);
        $this->assertSame($post1, $post2);

        $this->assertSame($address1, $address3);
        $this->assertSame($post1, $post3);
    }
}
