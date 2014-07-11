<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\BlogPost;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @group integration
 * @group integration.inheritence
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InheritenceTest extends AbstractDbTestCase
{
    public function testInheritence()
    {
        $post1 = Post::find(1);
        $post2 = BlogPost::find(1);

        $this->assertSame($post1, $post2);
    }
}
