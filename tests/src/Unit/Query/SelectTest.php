<?php

namespace Harp\Db\Test\Unit\Query;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Db\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Db\Query\Select
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Db\Test\Model\City');

        $select = new Select($repo);

        $this->assertSame($repo, $select->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $select->getFrom());
        $this->assertEquals([new SQL\Aliased('City.*')], $select->getColumns());
    }
}
