<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\LinkMap;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Rel\BelongsTo;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;

/**
 * @coversDefaultClass Harp\Harp\Repo\LinkMap
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LinkMapTest extends AbstractTestCase
{
    /**
     * @covers ::get
     * @covers ::has
     * @covers ::getRepo
     * @covers ::isEmpty
     * @covers ::__construct
     * @covers ::clear
     */
    public function testTest()
    {
        $map = new LinkMap(City::getRepo());
        $model = new City();

        $this->assertFalse($map->has($model));
        $this->assertSame(City::getRepo(), $map->getRepo());
        $this->assertTrue($map->isEmpty($model));

        $links = $map->get($model);

        $this->assertInstanceOf('Harp\Harp\Repo\Links', $links);
        $this->assertEmpty($links->all());
        $this->assertTrue($map->has($model));
        $this->assertTrue($map->isEmpty($model));

        $link = new LinkOne(
            $model,
            new BelongsTo('one', City::getRepo()->getConfig(), City::getRepo()),
            new City()
        );

        $links->add($link);

        $this->assertTrue($map->has($model));
        $this->assertFalse($map->isEmpty($model));

        $links2 = $map->get($model);

        $this->assertSame($links, $links2);

        $map->clear();

        $this->assertFalse($map->has($model));
    }

    /**
     * @covers ::addLink
     */
    public function testAddLink()
    {
        $model = new City();
        $foreign = new City();
        $link = new LinkOne(
            $model,
            new BelongsTo('one', City::getRepo()->getConfig(), City::getRepo()),
            $foreign
        );

        $map = new LinkMap(City::getRepo());

        $map->addLink($link);

        $this->assertSame($link, $map->get($model)->get('one'));
    }

    /**
     * @covers ::get
     * @expectedException InvalidArgumentException
     */
    public function testInvalidModel()
    {
        $map = new LinkMap(City::getRepo());
        $model = new Country();

        $map->get($model);
    }
}
