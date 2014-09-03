<?php

namespace Harp\Harp\Test;

use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\BlogPost;
use Harp\Harp\Test\Integration\SaveableObject;
use Harp\Harp\Repo\Event;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Rel;
use Harp\Harp\Repo\Container;
use Harp\Harp\Model\State;
use Harp\Harp\Model\Models;

/**
 * @coversDefaultClass Harp\Harp\Repo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoTest extends AbstractDbTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getConfig
     * @covers ::getIdentityMap
     * @covers ::getLinkMap
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\TestModel\City');

        $this->assertInstanceof('Harp\Harp\Config', $repo->getConfig());
        $this->assertEquals(__NAMESPACE__.'\TestModel\City', $repo->getConfig()->getModelClass());

        $this->assertInstanceof('Harp\IdentityMap\IdentityMap', $repo->getIdentityMap());

        $this->assertInstanceof('Harp\Harp\Repo\LinkMap', $repo->getLinkMap());
        $this->assertSame($repo, $repo->getLinkMap()->getRepo());
    }

    /**
     * @covers ::initializeModel
     */
    public function testInitializeModel()
    {
        $user = new User();

        $user->object = 'C:41:"Harp\\Harp\\Test\\Integration\\SaveableObject":22:{a:1:{i:0;s:5:"test1";}}';

        $expected = new SaveableObject();
        $expected->setVar('test1');

        User::getRepo()->initializeModel($user);

        $this->assertEquals($expected, $user->object);

        $post = new BlogPost(['class' => null]);

        BlogPost::getRepo()->initializeModel($post);

        $this->assertEquals('Harp\Harp\Test\TestModel\BlogPost', $post->class);

    }

    /**
     * @covers ::dispatchBeforeEvent
     * @covers ::dispatchAfterEvent
     */
    public function testDispatchEvents()
    {
        $eventListener = $this->getMock('Harp\Harp\Repo\EventListeners');

        $city = new City();

        $repo = $this->getMock('Harp\Harp\Repo', ['getEventListeners'], [__NAMESPACE__.'\TestModel\City']);

        $repo
            ->expects($this->exactly(2))
            ->method('getEventListeners')
            ->will($this->returnValue($eventListener));

        $eventListener
            ->expects($this->once())
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($city), $this->equalTo(Event::SAVE));

        $repo->dispatchBeforeEvent($city, Event::SAVE);

        $eventListener
            ->expects($this->once())
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($city), $this->equalTo(Event::SAVE));

        $repo->dispatchAfterEvent($city, Event::SAVE);
    }

    /**
     * @covers ::newModel
     */
    public function testNewModel()
    {
        $repo = new Repo(__NAMESPACE__.'\TestModel\City');

        $model = $repo->newModel();

        $this->assertInstanceOf(__NAMESPACE__.'\TestModel\City', $model);
        $this->assertTrue($model->isPending());

        $model = $repo->newModel(['id' => 10, 'name' => 'new'], State::SAVED);

        $this->assertEquals(['id' => 10, 'name' => 'new', 'countryId' => null], $model->getProperties());
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers ::newVoidModel
     */
    public function testNewVoidModel()
    {
        $repo = new Repo(__NAMESPACE__.'\TestModel\City');

        $model = $repo->newVoidModel();

        $this->assertInstanceOf(__NAMESPACE__.'\TestModel\City', $model);
        $this->assertTrue($model->isVoid());

        $model = $repo->newVoidModel(['id' => 10, 'name' => 'new']);

        $this->assertEquals(['id' => 10, 'name' => 'new', 'countryId' => null], $model->getProperties());
        $this->assertTrue($model->isVoid());
    }


    /**
     * @covers ::addLink
     * @covers ::loadLink
     */
    public function testAddLink()
    {
        $city = new City();
        $country = new Country();
        $link = new LinkOne($city, City::getRepo()->getRel('country'), $country);

        City::getRepo()->addLink($link);

        $this->assertSame($link, City::getRepo()->loadLink($city, 'country'));
    }

    /**
     * @covers ::getRootRepo
     */
    public function testGetRootRepo()
    {
        $this->assertSame(Post::getRepo(), Post::getRepo()->getRootRepo());
        $this->assertSame(Post::getRepo(), BlogPost::getRepo()->getRootRepo());
    }

    /**
     * @covers ::loadLink
     */
    public function testLoadLink()
    {
        $repo = City::getRepo();

        $city = City::find(1);
        $country = Country::find(1);

        $link = $repo->loadLink($city, 'country', State::DELETED);

        $this->assertSame($country, $link->get());
    }

    /**
     * @covers ::loadRelFor
     */
    public function testLoadRelFor()
    {
        $citiesArray = [new City(), new City()];
        $countriesArray = [new Country(), new Country()];

        $cities = new Models($citiesArray);
        $countries = new Models($countriesArray);

        $mockRel = $this->getMock(
            'Harp\Harp\Rel\BelongsTo',
            ['loadModelsIfAvailable', 'areLinked'],
            ['country', City::getRepo()->getConfig(), Country::getRepo()]
        );

        City::getRepo()->getConfig()->addRel($mockRel);

        $mockRel
            ->expects($this->once())
            ->method('loadModelsIfAvailable')
            ->with($this->identicalTo($cities), $this->equalTo(State::DELETED))
            ->will($this->returnValue($countries));

        $map = [
            [$citiesArray[0], $countriesArray[0], true],
            [$citiesArray[1], $countriesArray[0], false],
            [$citiesArray[0], $countriesArray[1], false],
            [$citiesArray[1], $countriesArray[1], true],
        ];

        $mockRel
            ->expects($this->exactly(4))
            ->method('areLinked')
            ->will($this->returnValueMap($map));

        $result = City::getRepo()->loadRelFor($cities, 'country', State::DELETED);

        $this->assertSame($countries, $result);

        $link1 = City::getRepo()->loadLink($citiesArray[0], 'country');
        $link2 = City::getRepo()->loadLink($citiesArray[1], 'country');

        $this->assertSame($countriesArray[0], $link1->get());
        $this->assertSame($countriesArray[1], $link2->get());
    }


    /**
     * @covers ::loadAllRelsFor
     */
    public function testLoadAllRelsFor()
    {
        $repo1 = $this->getMock('Harp\Harp\Repo', ['loadRelFor'], [__NAMESPACE__.'\TestModel\City']);
        $repo2 = $this->getMock('Harp\Harp\Repo', ['loadRelFor'], [__NAMESPACE__.'\TestModel\Country']);
        $repo3 = $this->getMock('Harp\Harp\Repo', ['loadRelFor'], [__NAMESPACE__.'\TestModel\User']);

        Container::set(__NAMESPACE__.'\TestModel\City', $repo1);
        Container::set(__NAMESPACE__.'\TestModel\Country', $repo2);
        Container::set(__NAMESPACE__.'\TestModel\User', $repo3);

        $repo1->getConfig()->addRel(new Rel\BelongsTo('one', $repo1->getConfig(), $repo2));
        $repo2->getConfig()->addRel(new Rel\HasMany('many', $repo2->getConfig(), $repo3));

        $models1 = new Models([new City()]);
        $models2 = new Models([new Country()]);
        $models3 = new Models([new User()]);

        $repo1
            ->expects($this->once())
            ->method('loadRelFor')
            ->with($this->equalTo($models1), $this->equalTo('one'), $this->equalTo(State::DELETED))
            ->will($this->returnValue($models2));

        $repo2
            ->expects($this->once())
            ->method('loadRelFor')
            ->with($this->equalTo($models2), $this->equalTo('many'), $this->equalTo(State::DELETED))
            ->will($this->returnValue($models3));

        $repo1->loadAllRelsFor($models1, ['one' => 'many'], State::DELETED);
    }


    /**
     * @covers ::updateModels
     */
    public function testUpdateModels()
    {
        $repo = $this->getMock(
            'Harp\Harp\Repo',
            ['updateAll', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\TestModel\City']
        );

        $update = $this->getMock('Harp\Harp\Query\Update', ['executeModels'], [$repo]);

        $models = [
            new City(null, State::SAVED),
            new User(['deletedAt' => time()], State::DELETED)
        ];

        $modelsObject = new Models($models);

        $update
            ->expects($this->once())
            ->method('executeModels')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->once())
            ->method('updateAll')
            ->will($this->returnValue($update));

        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::UPDATE)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(2))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::UPDATE)));

        $repo
            ->expects($this->at(5))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(6))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo->updateModels($modelsObject);
    }


    /**
     * @covers ::deleteModels
     */
    public function testDeleteModels()
    {
        $repo = $this->getMock(
            'Harp\Harp\Repo',
            ['deleteAll', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\TestModel\City']
        );

        $delete = $this->getMock('Harp\Harp\Query\Delete', ['executeModels'], [$repo]);

        $models = [
            new City(null, State::SAVED),
            new City(null, State::SAVED),
        ];

        $modelsObject = new Models($models);

        $delete
            ->expects($this->once())
            ->method('executeModels')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->once())
            ->method('deleteAll')
            ->will($this->returnValue($delete));

        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(3))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::DELETE)));

        $repo
            ->expects($this->at(4))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::DELETE)));

        $repo->deleteModels($modelsObject);
    }


    /**
     * @covers ::insertModels
     */
    public function testInsertModels()
    {
        $repo = $this->getMock(
            'Harp\Harp\Repo',
            ['insertAll', 'dispatchBeforeEvent', 'dispatchAfterEvent'],
            [__NAMESPACE__.'\TestModel\City']
        );

        $insert = $this->getMock('Harp\Harp\Query\Insert', ['executeModels'], [$repo]);

        $models = [
            new City(),
            new City(),
        ];

        $modelsObject = new Models($models);

        $insert
            ->expects($this->once())
            ->method('executeModels')
            ->with($this->identicalTo($modelsObject));

        $repo
            ->expects($this->once())
            ->method('insertAll')
            ->will($this->returnValue($insert));


        $repo
            ->expects($this->at(0))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(1))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(2))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(3))
            ->method('dispatchBeforeEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(5))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(6))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[0], $this->equalTo(Event::SAVE)));

        $repo
            ->expects($this->at(7))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::INSERT)));

        $repo
            ->expects($this->at(8))
            ->method('dispatchAfterEvent')
            ->with($this->identicalTo($models[1], $this->equalTo(Event::SAVE)));

        $repo->insertModels($modelsObject);

        foreach ($models as $model) {
            $this->assertTrue($model->isSaved());
        }
    }
}
