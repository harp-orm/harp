<?php

namespace Harp\Harp\Test;

use Harp\Harp\Test\TestModel\Tag;

/**
 * @package Jam
 * @author Ivan Kerin
 */
class TestTest extends AbstractTestCase
{
    public function testTest()
    {
        $session = $this->getSession();

        $select = $session->selectAll('Post');

        $model = $select->fetchFirst();

        $session->delete($model);

        $session->commit();
    }
}
