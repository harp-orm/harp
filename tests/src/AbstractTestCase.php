<?php

namespace Harp\Harp\Test;

use Harp\Harp\Repo\Container;
use PHPUnit_Framework_TestCase;

/**
 * @package Jam
 * @author Ivan Kerin
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        Container::clear();
    }
}
