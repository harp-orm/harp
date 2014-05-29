<?php

namespace Harp\Db\Test\Unit\Rel;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Model\Models;
use Harp\Core\Model\State;
use Harp\Db\Rel\HasManyExclusive;
use Harp\Db\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Db\Rel\HasManyExclusive
 */
class HasManyExclusiveTest extends AbstractTestCase
{
    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $rel = new HasManyExclusive('test', Repo\Country::get(), Repo\City::get());

        $model = new Model\Country();
        $foreign1 = new Model\City([], State::SAVED);
        $foreign2 = new Model\City([], State::SAVED);
        $foreign3 = new Model\City([], State::SAVED);
        $link = new LinkMany($rel, [$foreign1, $foreign2]);
        $link->add($foreign3);
        $link->remove($foreign2);

        $result = $rel->delete($model, $link);

        $this->assertInstanceOf('Harp\Core\Model\Models', $result);

        $this->assertSame([$foreign2], $result->toArray());
        $this->assertTrue($result->getFirst()->isDeleted());
    }


    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = new HasManyExclusive('cities', Repo\Country::get(), Repo\City::get());

        $model = new Model\Country(['id' => 2]);
        $foreign1 = new Model\City(['countryId' => 2]);
        $foreign2 = new Model\City(['countryId' => 2]);
        $foreign3 = new Model\City(['countryId' => 8]);

        $link = new LinkMany($rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $rel->update($model, $link);

        $this->assertEquals(2, $foreign2->countryId);
        $this->assertEquals(2, $foreign3->countryId);
    }
}
