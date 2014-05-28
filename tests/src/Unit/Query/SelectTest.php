<?php

namespace CL\Luna\Test\Unit\Query;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Model\Models;
use CL\Atlas\SQL;
use CL\Luna\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\Luna\Query\Select
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('CL\Luna\Test\Model\City');

        $select = new Select($repo);

        $this->assertSame($repo, $select->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $select->getFrom());
        $this->assertEquals([new SQL\Aliased('City.*')], $select->getColumns());
    }
}
