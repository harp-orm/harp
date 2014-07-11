<?php

namespace Harp\Harp\Test\Query;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Country;
use Harp\Harp\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Insert;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Insert
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InsertTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = City::getRepo();

        $insert = new Insert($repo);

        $this->assertSame($repo, $insert->getRepo());
        $this->assertEquals(new SQL\Aliased('City'), $insert->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = Country::getRepo();

        $insert = new Insert($repo);

        $models = new Models([new Country(['name' => 'test']), new City(['name' => 'test2'])]);

        $insert->models($models);

        $this->assertEquals(
            'INSERT INTO `Country` (`id`, `name`) VALUES (NULL, "test"), (NULL, "test2")',
            $insert->humanize()
        );
    }

    /**
     * @covers ::executeModels
     */
    public function testExecuteModels()
    {
        $models = new Models([new City(), new City()]);

        $insert = $this->getMock(
            'Harp\Harp\Query\Insert',
            ['models', 'execute', 'getLastInsertId'],
            [City::getRepo()]
        );

        $insert
            ->expects($this->at(0))
            ->method('models')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $insert
            ->expects($this->at(1))
            ->method('execute');

        $insert
            ->expects($this->at(2))
            ->method('getLastInsertId')
            ->will($this->returnValue(32));

        $insert->executeModels($models);

        $this->assertEquals(32, $models->getFirst()->getId());
        $this->assertEquals(33, $models->getNext()->getId());
    }
}
