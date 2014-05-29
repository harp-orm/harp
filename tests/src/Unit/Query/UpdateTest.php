<?php

namespace Harp\Db\Test\Unit\Query;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Model\Models;
use Harp\Core\Model\State;
use Harp\Query\SQL;
use Harp\Db\Query\Update;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Db\Query\Update
 */
class UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Db\Test\Model\City');

        $Update = new Update($repo);

        $this->assertSame($repo, $Update->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $Update->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\Country('Harp\Db\Test\Model\Country');

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
