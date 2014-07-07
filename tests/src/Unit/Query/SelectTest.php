<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Select
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SelectTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = Model\City::getRepo();

        $select = new Select($repo);

        $this->assertSame($repo, $select->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $select->getFrom());
        $this->assertEquals([new SQL\Aliased(new SQL\SQL('`City`.*'))], $select->getColumns());
    }
}
