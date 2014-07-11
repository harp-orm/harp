<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.custom_link_class
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class CustomLinkClassTest extends AbstractDbTestCase
{
    public function testTest()
    {
        $user1 = User::find(1);

        $posts = $user1->getPosts();

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\LinkManyPosts', $posts);
    }
}
