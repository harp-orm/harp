<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Rel\BelongsTo;

/**
 * @coversDefaultClass Harp\Harp\Repo\AbstractLink
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getModel
     * @covers ::getRel
     */
    public function testConstruct()
    {
        $city = new City();
        $rel = new BelongsTo('test', City::getRepo()->getConfig(), City::getRepo());

        $link = $this->getMockForAbstractClass('Harp\Harp\Repo\AbstractLink', [$city, $rel]);
        $this->assertSame($rel, $link->getRel());
        $this->assertSame($city, $link->getModel());
    }
}
