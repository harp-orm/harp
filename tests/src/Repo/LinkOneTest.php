<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Profile;

/**
 * @coversDefaultClass Harp\Harp\Repo\LinkOne
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LinkOneTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getOriginal
     * @covers ::getRel
     * @covers ::get
     */
    public function testConstruct()
    {
        $rel = City::getRepo()->getRel('country');
        $model = new Country();

        $link = new LinkOne(new City(), $rel, $model);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($model, $link->get());
        $this->assertSame($model, $link->getOriginal());
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $rel = $this->getMock(
            __NAMESPACE__.'\TestBelongsTo',
            ['delete'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $result = $link->delete();
        $this->assertSame($models, $result);
    }

    /**
     * @covers ::delete
     */
    public function testNoDelete()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\BelongsTo',
            ['delete'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $result = $link->delete();
        $this->assertNull($result);
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $rel = $this->getMock(
            __NAMESPACE__.'\TestBelongsTo',
            ['insert'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $result = $link->insert();
        $this->assertSame($models, $result);
    }

    /**
     * @covers ::insert
     */
    public function testNoInsert()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\BelongsTo',
            ['delete'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $result = $link->insert();
        $this->assertNull($result);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = $this->getMock(
            'Harp\Harp\Rel\BelongsTo',
            ['update'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($link))
            ->will($this->returnValue($models));

        $result = $link->update();
        $this->assertSame($models, $result);

    }

    /**
     * @covers ::update
     */
    public function testNoUpdate()
    {
        $rel = $this->getMock(
            __NAMESPACE__.'\TestBelongsTo',
            ['update'],
            ['test', City::getRepo()->getConfig(), Country::getRepo()]
        );

        $link = new LinkOne(new City, $rel, new Country());

        $result = $link->update();
        $this->assertNull($result);
    }

    /**
     * @covers ::set
     * @covers ::get
     * @covers ::isChanged
     * @covers ::getOriginal
     */
    public function testSet()
    {
        $model = new Country();
        $model2 = new Country();

        $link = new LinkOne(new City(), City::getRepo()->getRel('country'), $model);

        $this->assertFalse($link->isChanged());

        $link->set($model);

        $this->assertFalse($link->isChanged());

        $link->set($model2);

        $this->assertTrue($link->isChanged());
        $this->assertSame($model2, $link->get());
        $this->assertSame($model, $link->getOriginal());
    }

    /**
     * @covers ::set
     */
    public function testSetInverse()
    {
        $user = new User();
        $profile = new Profile();

        $user->setProfile($profile);

        $this->assertSame($user, $profile->getUser());
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $model = new Country();
        $link = new LinkOne(new City(), City::getRepo()->getRel('country'), $model);
        $link->clear();

        $this->assertTrue($link->get()->isVoid());
    }

    /**
     * @covers ::getCurrentAndOriginal
     */
    public function testGetCurrentAndOriginal()
    {
        $model = new Country();
        $model2 = new Country();
        $link = new LinkOne(new City(), City::getRepo()->getRel('country'), $model);

        $result = $link->getCurrentAndOriginal();

        $this->assertTrue($result->has($model));
        $this->assertCount(1, $result);

        $link->set($model2);

        $result = $link->getCurrentAndOriginal();

        $this->assertTrue($result->has($model));
        $this->assertTrue($result->has($model2));
        $this->assertCount(2, $result);
    }

}
