<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Insert;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\Insert
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InsertTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = Model\City::getRepo();

        $insert = new Insert($repo);

        $this->assertSame($repo, $insert->getRepo());
        $this->assertEquals(new SQL\Aliased('City'), $insert->getTable());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = Model\Country::getRepo();

        $insert = new Insert($repo);

        $models = new Models([new Model\Country(['name' => 'test']), new Model\City(['name' => 'test2'])]);

        $insert->models($models);

        $this->assertEquals(
            'INSERT INTO `Country` (`id`, `name`) VALUES (NULL, "test"), (NULL, "test2")',
            $insert->humanize()
        );
    }
}
