<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Model\State;
use Harp\Harp\Rel\HasManyExclusive;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasManyExclusive
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyExclusiveTest extends AbstractDbTestCase
{
    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $rel = new HasManyExclusive('test', Country::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\City');

        $model = new Country();
        $foreign1 = new City([], State::SAVED);
        $foreign2 = new City([], State::SAVED);
        $foreign3 = new City([], State::SAVED);
        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->add($foreign3);
        $link->remove($foreign2);

        $result = $rel->delete($link);

        $this->assertInstanceOf('Harp\Harp\Model\Models', $result);

        $this->assertSame([$foreign2], $result->toArray());
        $this->assertTrue($result->getFirst()->isDeleted());
    }


    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasManyExclusive('cities', Country::getRepo()->getConfig(), 'Harp\Harp\Test\TestModel\City');

        $model = new Country(['id' => 2]);
        $foreign1 = new City(['countryId' => 2]);
        $foreign2 = new City(['countryId' => 2]);
        $foreign3 = new City(['countryId' => 8]);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($link);

        $this->assertEquals(2, $foreign2->countryId);
        $this->assertEquals(2, $foreign3->countryId);
    }
}
