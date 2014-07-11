<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Model\Models;
use Harp\Util\Objects;

/**
 * @coversDefaultClass Harp\Harp\Rel\AbstractRelMany
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelManyTest extends AbstractTestCase
{
    public function getRel()
    {
        return $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRelMany',
            ['test name', City::getRepo()->getConfig(), City::getRepo()]
        );
    }

    /**
     * @covers ::getLinkClass
     * @covers ::setLinkClass
     */
    public function testCustomClass()
    {
        $rel = $this->getRel();
        $rel->setLinkClass('Harp\Harp\Test\TestModel\LinkManyPosts');

        $this->assertEquals('Harp\Harp\Test\TestModel\LinkManyPosts', $rel->getLinkClass());

        $this->setExpectedException('InvalidArgumentException');

        $rel->setLinkClass('Harp\Harp\Test\TestModel\City');
    }

    /**
     * @covers ::newLinkFrom
     */
    public function testNewLinkFrom()
    {
        $expected = [new City(['id' => 1]), new City(['id' => 2])];
        $expected2 = [new City(['id' => 1]), new City(['id' => 2])];
        $expected3 = [];
        $model = new City();

        $rel = $this->getRel();
        $result = $rel->newLinkFrom($model, $expected);

        $this->assertInstanceof('Harp\Harp\Repo\LinkMany', $result);
        $this->assertSame($expected, $result->toArray());

        $rel->setLinkClass('Harp\Harp\Test\TestModel\LinkManyPosts');
        $result = $rel->newLinkFrom($model, $expected);

        $this->assertInstanceof('Harp\Harp\Test\TestModel\LinkManyPosts', $result);
        $this->assertSame($expected, $result->toArray());

        $result2 = $rel->newLinkFrom($model, $expected2);

        $this->assertSame($expected2, $result2->toArray(), 'Should pass through identity mapper');

        $result3 = $rel->newLinkFrom($model, $expected3);

        $this->assertSame($expected3, $result3->toArray(), 'Should allow empty');
    }
}
