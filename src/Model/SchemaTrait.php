<?php

namespace CL\Luna\Model;

use CL\Luna\ModelQuery\Select;
use CL\Luna\ModelQuery\Delete;
use CL\Luna\ModelQuery\Update;
use CL\Luna\ModelQuery\Insert;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SchemaTrait
{
    private static $schema;

    public static function getName()
    {
        return self::getSchema()->getName();
    }

    public static function getPrimaryKey()
    {
        return self::getSchema()->getPrimaryKey();
    }

    public static function getTable()
    {
        return self::getSchema()->getTable();
    }

    public static function getSoftDelete()
    {
        return self::getSchema()->getSoftDelete();
    }

    public static function getDb()
    {
        return self::getSchema()->getDb();
    }

    public static function getPropertyNames()
    {
        return self::getSchema()->getPropertyNames();
    }

    public static function getFields()
    {
        return self::getSchema()->getFields();
    }

    public static function getField($name)
    {
        return self::getSchema()->getField($name);
    }

    public static function getRels()
    {
        return self::getSchema()->getRels();
    }

    public static function getRel($name)
    {
        return self::getSchema()->getRel($name);
    }

    public static function getValidators()
    {
        return self::getSchema()->getValidators();
    }

    public static function getSchema()
    {
        if (! self::$schema) {
            self::$schema = new Schema(get_called_class());
        }

        return self::$schema;
    }

    public static function find($id)
    {
        return static::findAll()
            ->whereKey($id)
            ->loadFirst();
    }

    public static function findAll()
    {
        return new Select(self::getSchema());
    }

    public static function deleteAll()
    {
        return new Delete(self::getSchema());
    }

    public static function updateAll()
    {
        return new Update(self::getSchema());
    }

    public static function insertAll()
    {
        return new Insert(self::getSchema());
    }
}
