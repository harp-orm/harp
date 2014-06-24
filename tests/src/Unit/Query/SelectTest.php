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
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
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
