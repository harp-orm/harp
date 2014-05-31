<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Insert;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Insert
 */
class InsertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Harp\Test\Model\City');

        $insert = new Insert($repo);

        $this->assertSame($repo, $insert->getRepo());
        $this->assertEquals(new SQL\Aliased('City'), $insert->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\Country('Harp\Harp\Test\Model\Country');

        $insert = new Insert($repo);

        $models = new Models([new Model\Country(['name' => 'test']), new Model\City(['name' => 'test2'])]);

        $insert->models($models);

        $this->assertEquals(
            'INSERT INTO Country (id, name) VALUES (NULL, "test"), (NULL, "test2")',
            $insert->humanize()
        );
    }
}
