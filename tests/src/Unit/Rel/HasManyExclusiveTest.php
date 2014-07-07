<?php

namespace Harp\Harp\Test\Unit\Rel;

use Harp\Harp\Test\Model;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Model\Models;
use Harp\Core\Model\State;
use Harp\Harp\Rel\HasManyExclusive;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasManyExclusive
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyExclusiveTest extends AbstractTestCase
{
    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $rel = new HasManyExclusive('test', Model\Country::getRepo(), Model\City::getRepo());

        $model = new Model\Country();
        $foreign1 = new Model\City([], State::SAVED);
        $foreign2 = new Model\City([], State::SAVED);
        $foreign3 = new Model\City([], State::SAVED);
        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->add($foreign3);
        $link->remove($foreign2);

        $result = $rel->delete($link);

        $this->assertInstanceOf('Harp\Core\Model\Models', $result);

        $this->assertSame([$foreign2], $result->toArray());
        $this->assertTrue($result->getFirst()->isDeleted());
    }


    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasManyExclusive('cities', Model\Country::getRepo(), Model\City::getRepo());

        $model = new Model\Country(['id' => 2]);
        $foreign1 = new Model\City(['countryId' => 2]);
        $foreign2 = new Model\City(['countryId' => 2]);
        $foreign3 = new Model\City(['countryId' => 8]);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($link);

        $this->assertEquals(2, $foreign2->countryId);
        $this->assertEquals(2, $foreign3->countryId);
    }
}
