<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Util\Objects;

/**
 * @coversDefaultClass Harp\Harp\Rel\AbstractRelOne
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelOneTest extends AbstractTestCase
{

    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRelOne',
            ['test name', City::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\City']
        );
    }

    /**
     * @covers ::newLinkFrom
     */
    public function testNewLink()
    {
        $expected = new City(['id' => 1]);
        $expected2 = new City(['id' => 1]);
        $model = new City();

        $rel = $this->getRel();
        $result = $rel->newLinkFrom($model, [$expected]);

        $this->assertInstanceof('Harp\Harp\Repo\LinkOne', $result);
        $this->assertSame($rel, $result->getRel());
        $this->assertSame($expected, $result->get());

        $result2 = $rel->newLinkFrom($model, [$expected2]);

        $this->assertSame($expected2, $result2->get());

        $result3 = $rel->newLinkFrom($model, []);

        $this->assertInstanceof('Harp\Harp\Repo\LinkOne', $result3);
        $this->assertInstanceof('Harp\Harp\Test\TestModel\City', $result3->get());
        $this->assertTrue($result3->get()->isVoid());
    }

    /**
     * @covers ::updateInverse
     */
    public function testUpdateInverse()
    {
        $user = new User();
        $post = new Post();

        $post->getRepo()->getRel('user')->updateInverse($user, $post);

        $this->assertSame($post->getUser(), $user);
    }
}
