<?php

namespace CL\Luna\Test\Unit\Query;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Model\Models;
use CL\Atlas\SQL;
use CL\Luna\Query\Delete;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\Luna\Query\Delete
 */
class DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('CL\Luna\Test\Model\City');

        $delete = new Delete($repo);

        $this->assertSame($repo, $delete->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $delete->getFrom());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\City('CL\Luna\Test\Model\City');

        $delete = new Delete($repo);

        $models = new Models([new Model\City(['id' => 5]), new Model\City(['id' => 12])]);

        $delete->models($models);

        $this->assertEquals('DELETE FROM City WHERE (id IN (5, 12))',$delete->humanize());
    }
}
