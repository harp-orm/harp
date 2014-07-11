<?php

namespace Harp\Harp\Test\Unit\Model;

use Harp\Harp\Model\RepoTrait;
use Harp\Harp\Test\AbstractDbTestCase;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Model\State;

/**
 * @coversDefaultClass Harp\Harp\Model\RepoTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoTraitTest extends AbstractDbTestCase
{
    /**
     * @covers ::getRepo
     */
    public function testGetRepo()
    {
        $repo1 = User::getRepo();
        $repo2 = Address::getRepo();

        $this->assertInstanceOf('Harp\Harp\Repo', $repo1);
        $this->assertInstanceOf('Harp\Harp\Repo', $repo2);
        $this->assertNotSame($repo1, $repo2);

        $this->assertEquals('Harp\Harp\Test\TestModel\User', $repo1->getModelClass());
        $this->assertEquals('Harp\Harp\Test\TestModel\Address', $repo2->getModelClass());
    }

    /**
     * @covers ::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $this->assertEquals('id', User::getPrimaryKey());
    }

    /**
     * @covers ::getNameKey
     */
    public function testGetNameKey()
    {
        $this->assertEquals('name', User::getNameKey());
    }

    /**
     * @covers ::find
     */
    public function testFind()
    {
        $user = User::find(5, State::DELETED);

        $this->assertEquals('deleted', $user->name);
    }

    /**
     * @covers ::findByName
     */
    public function testFindByName()
    {
        $user = User::findByName('deleted', State::DELETED);

        $this->assertEquals('deleted', $user->name);
    }

    public function dataFindProxy()
    {
        return [
            ['where', 'id', 5,             'SELECT `User`.* FROM `User` WHERE (`id` = 5)'],
            ['whereRaw', 'id < ?', [5],    'SELECT `User`.* FROM `User` WHERE (id < 5)'],
            ['whereIn', 'id', [1, 5],      'SELECT `User`.* FROM `User` WHERE (`id` IN (1, 5))'],
            ['whereNot', 'id', 8,          'SELECT `User`.* FROM `User` WHERE (`id` != 8)'],
            ['whereLike', 'name', '%test', 'SELECT `User`.* FROM `User` WHERE (`name` LIKE "%test")'],
        ];
    }

    /**
     * @dataProvider dataFindProxy
     * @covers ::where
     * @covers ::whereRaw
     * @covers ::whereIn
     * @covers ::whereNot
     * @covers ::whereLike
     */
    public function testFindProxy($method, $column, $argument, $expected)
    {
        $this->assertEquals($expected, User::$method($column, $argument)->humanize());
    }

    public function dataQueries()
    {
        return [
            ['selectAll', 'Harp\Harp\Query\Select'],
            ['deleteAll', 'Harp\Harp\Query\Delete'],
            ['updateAll', 'Harp\Harp\Query\Update'],
            ['insertAll', 'Harp\Harp\Query\Insert'],
            ['findAll',   'Harp\Harp\Find'],
        ];
    }

    /**
     * @dataProvider dataQueries
     */
    public function testQueries($method, $expectedClass)
    {
        $query = User::$method();

        $this->assertInstanceOf($expectedClass, $query);

        $this->assertSame(User::getRepo(), $query->getRepo());
    }

    /**
     * @covers ::save
     */
    public function testSave()
    {
        $user = User::find(1);

        $user->name = 'new name';

        User::save($user);

        User::getRepo()->getIdentityMap()->clear();

        $user = User::find(1);

        $this->assertEquals('new name', $user->name);
    }

    /**
     * @covers ::saveArray
     */
    public function testSaveArray()
    {
        $posts = Post::findAll()->load()->toArray();

        $posts[0]->name = 'new post name 1';
        $posts[1]->name = 'new post name 2';

        Post::saveArray($posts);

        User::getRepo()->getIdentityMap()->clear();

        $posts = Post::findAll()->load();

        $this->assertEquals('new post name 1', $posts->getFirst()->name);
        $this->assertEquals('new post name 2', $posts->getNext()->name);
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testGetSetId()
    {
        $post = Post::find(2);

        $this->assertEquals(2, $post->getId());

        $post->setId(4);

        $this->assertEquals(4, $post->getId());
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $user = User::find(1);

        $link = $user->getLink('address');

        $this->assertSame($user, $link->getModel());
        $this->assertSame(Address::getRepo(), $link->getRel()->getRepo());
    }

    /**
     * @covers ::getLinkOne
     * @covers ::get
     * @covers ::set
     */
    public function testGetLinkOne()
    {
        $city = City::find(1);
        $country = Country::find(1);
        $country2 = Country::find(2);

        $link = $city->getLinkOne('country');
        $this->assertInstanceOf('Harp\Harp\Repo\LinkOne', $link);
        $this->assertSame($city, $link->getModel());
        $this->assertSame($country, $link->get());

        $model = $city->get('country');
        $this->assertSame($country, $model);

        $city->set('country', $country2);

        $this->assertSame($country2, $link->get());

        $this->setExpectedException('LogicException', 'Rel cities for Harp\Harp\Test\TestModel\Country must be a valid RelOne');

        $country->get('cities');
    }

    /**
     * @covers ::all
     */
    public function testGetLinkMany()
    {
        $country = Country::find(1);
        $city = City::find(1);

        $cities = $country->all('cities');

        $this->assertSame($cities, Country::getRepo()->loadLink($country, 'cities'));

        $this->setExpectedException('LogicException', 'Rel country for Harp\Harp\Test\TestModel\City must be a valid RelMany');

        $city->all('country');
    }
}
