<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\Post;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InheritedTraitTest extends AbstractTestCase
{
    /**
     * @covers Harp\Harp\Model\InheritedTrait
     */
    public function testUpdateInheritanceClass()
    {
        $model = new Post();

        $this->assertEquals('Harp\Harp\Test\TestModel\Post', $model->class);
    }
}
