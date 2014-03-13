<?php namespace CL\Luna\Schema;

use CL\Luna\Schema\Query\Select;
use CL\Luna\Schema\Query\Update;
use CL\Luna\Schema\Query\Insert;
use CL\Luna\Schema\Query\Delete;

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

    public static function getRels()
    {
        return self::getSchema()->getRels();
    }

    public static function getValidators()
    {
        return self::getSchema()->getValidators();
    }

    public static function getSchema()
    {
        self::initializeSchema();

        return self::$schema;
    }

    public static function get($id)
    {
        return static::all()
            ->whereKey($id)
            ->first();
    }

    public static function all()
    {
        return static::getSchema()->getSelectSchema();
    }

    public static function deleteAll()
    {
        return static::getSchema()->getDeleteSchema();
    }

    public static function update()
    {
        return static::getSchema()->getUpdateSchema();
    }

    public static function insert()
    {
        return static::getSchema()->getInsertSchema();
    }

    public static function initializeSchema()
    {
        if ( ! self::$schema)
        {
            self::$schema = new Schema(get_called_class());
        }
    }
}
