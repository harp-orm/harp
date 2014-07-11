<?php

namespace Harp\Harp\Test\Model;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class TestProxy
{
    public static function find($id, $flags = null)
    {
        return 'find';
    }

    public static function findByName($name, $flags = null)
    {
        return 'findByName';
    }

    public static function updateAll()
    {
        return 'updateAll';
    }

    public static function deleteAll()
    {
        return 'deleteAll';
    }

    public static function selectAll()
    {
        return 'selectAll';
    }

    public static function insertAll()
    {
        return 'insertAll';
    }

    public static function findAll()
    {
        return 'findAll';
    }
}
