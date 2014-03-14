<?php namespace CL\Luna\Schema;

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
        return static::getSchema()->getSelectQuery();
    }

    public static function deleteQuery()
    {
        return static::getSchema()->getDeleteQuery();
    }

    public static function updateQuery()
    {
        return static::getSchema()->getUpdateQuery();
    }

    public static function insertQuery()
    {
        return static::getSchema()->getInsertQuery();
    }

    public static function initializeSchema()
    {
        if ( ! self::$schema)
        {
            self::$schema = new Schema(get_called_class());
        }
    }
}
