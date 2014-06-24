<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Delete;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Delete
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Harp\Test\Model\City');

        $delete = new Delete($repo);

        $this->assertSame($repo, $delete->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $delete->getFrom());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\City('Harp\Harp\Test\Model\City');

        $delete = new Delete($repo);

        $models = new Models([new Model\City(['id' => 5]), new Model\City(['id' => 12])]);

        $delete->models($models);

        $this->assertEquals('DELETE FROM City WHERE (id IN (5, 12))',$delete->humanize());
    }
}
