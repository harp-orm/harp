<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Core\Model\State;
use Harp\Query\SQL;
use Harp\Harp\Query\Update;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Update
 */
class UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Harp\Test\Model\City');

        $Update = new Update($repo);

        $this->assertSame($repo, $Update->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $Update->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\Country('Harp\Harp\Test\Model\Country');

        $update = new Update($repo);

        $model1 = new Model\Country(['id' => 1], State::SAVED);
        $model1->name = 'test';

        $model2 = new Model\Country(['id' => 2], State::SAVED);
        $model2->name = 'test2';

        $models = new Models([$model1, $model2]);

        $update->models($models);

        $this->assertEquals(
            'UPDATE Country SET name = CASE id WHEN 1 THEN "test" WHEN 2 THEN "test2" ELSE name END WHERE (id IN (1, 2))',
            $update->humanize()
        );
    }
}
