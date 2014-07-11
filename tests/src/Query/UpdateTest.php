<?php

namespace Harp\Harp\Test\Query;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Model\Models;
use Harp\Harp\Model\State;
use Harp\Query\SQL;
use Harp\Harp\Query\Update;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Update
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class UpdateTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = City::getRepo();

        $update = new Update($repo);

        $this->assertSame($repo, $update->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $update->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = Country::getRepo();

        $update = new Update($repo);

        $model1 = new Country(['id' => 1], State::SAVED);
        $model1->name = 'test';

        $model2 = new Country(['id' => 2], State::SAVED);
        $model2->name = 'test2';

        $models = new Models([$model1, $model2]);

        $update->models($models);

        $this->assertEquals(
            'UPDATE `Country` SET `name` = CASE `id` WHEN 1 THEN "test" WHEN 2 THEN "test2" ELSE `name` END WHERE (`id` IN (1, 2))',
            $update->humanize()
        );
    }

    /**
     * @covers ::model
     */
    public function testModel()
    {
        $repo = Country::getRepo();

        $update = new Update($repo);

        $model = new Country(['id' => 1], State::SAVED);
        $model->name = 'test';

        $update->model($model);

        $this->assertEquals(
            'UPDATE `Country` SET `name` = "test" WHERE (`id` = 1)',
            $update->humanize()
        );
    }

    /**
     * @covers ::executeModels
     */
    public function testExecuteModels()
    {
        $models1 = new Models([new City(), new City()]);
        $models2 = new Models([new City()]);

        $update = $this->getMock(
            'Harp\Harp\Query\Update',
            ['models', 'model', 'execute'],
            [City::getRepo()]
        );

        $update
            ->expects($this->at(0))
            ->method('models')
            ->with($this->identicalTo($models1))
            ->will($this->returnSelf());

        $update
            ->expects($this->at(1))
            ->method('execute');

        $update
            ->expects($this->at(2))
            ->method('model')
            ->with($this->identicalTo($models2->getFirst()))
            ->will($this->returnSelf());

        $update->executeModels($models1);
        $update->executeModels($models2);
    }
}
