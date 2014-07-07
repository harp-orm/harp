<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Core\Model\State;
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
        $repo = Model\City::getRepo();

        $update = new Update($repo);

        $this->assertSame($repo, $update->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $update->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = Model\Country::getRepo();

        $update = new Update($repo);

        $model1 = new Model\Country(['id' => 1], State::SAVED);
        $model1->name = 'test';

        $model2 = new Model\Country(['id' => 2], State::SAVED);
        $model2->name = 'test2';

        $models = new Models([$model1, $model2]);

        $update->models($models);

        $this->assertEquals(
            'UPDATE `Country` SET `name` = CASE `id` WHEN 1 THEN "test" WHEN 2 THEN "test2" ELSE `name` END WHERE (`id` IN (1, 2))',
            $update->humanize()
        );
    }
}
