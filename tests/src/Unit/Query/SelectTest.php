<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Select
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Harp\Test\Model\City');

        $select = new Select($repo);

        $this->assertSame($repo, $select->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $select->getFrom());
        $this->assertEquals([new SQL\Aliased('City.*')], $select->getColumns());
    }
}
