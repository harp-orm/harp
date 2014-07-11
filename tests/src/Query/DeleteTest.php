<?php

namespace Harp\Harp\Test\Query;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Delete;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Delete
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class DeleteTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = City::getRepo();

        $delete = new Delete($repo);

        $this->assertSame($repo, $delete->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $delete->getFrom());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = City::getRepo();

        $delete = new Delete($repo);

        $models = new Models([new City(['id' => 5]), new City(['id' => 12])]);

        $delete->models($models);

        $this->assertEquals('DELETE FROM `City` WHERE (`id` IN (5, 12))',$delete->humanize());
    }

    /**
     * @covers ::executeModels
     */
    public function testExecuteModels()
    {
        $models = new Models([new City(['id' => 5]), new City(['id' => 12])]);

        $delete = $this->getMock('Harp\Harp\Query\Delete', ['models', 'execute'], [City::getRepo()]);

        $delete
            ->expects($this->at(0))
            ->method('models')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $delete
            ->expects($this->at(1))
            ->method('execute');

        $delete->executeModels($models);
    }
}
