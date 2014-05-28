<?php

namespace CL\Luna\Test\Unit\Query;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Model\Models;
use CL\Atlas\SQL;
use CL\Luna\Query\Insert;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\Luna\Query\Insert
 */
class InsertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('CL\Luna\Test\Model\City');

        $insert = new Insert($repo);

        $this->assertSame($repo, $insert->getRepo());
        $this->assertEquals(new SQL\Aliased('City'), $insert->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\Country('CL\Luna\Test\Model\Country');

        $insert = new Insert($repo);

        $models = new Models([new Model\Country(['name' => 'test']), new Model\City(['name' => 'test2'])]);

        $insert->models($models);

        $this->assertEquals(
            'INSERT INTO Country (id, name) VALUES (NULL, "test"), (NULL, "test2")',
            $insert->humanize()
        );
    }
}
