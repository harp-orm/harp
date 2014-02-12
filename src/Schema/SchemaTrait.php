<?php namespace CL\Luna\Schema;

use CL\Luna\DB\SelectSchema;
use CL\Luna\DB\UpdateSchema;
use CL\Luna\DB\InsertSchema;
use CL\Luna\DB\DeleteSchema;

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
		$result = static::all()
			->whereKey($id)
			->limit(1)
			->execute();

		return $result->fetch();
	}

	public static function all()
	{
		return new SelectSchema(static::getSchema());
	}

	public static function delete()
	{
		return new DeleteSchema(static::getSchema());
	}

	public static function update()
	{
		return new UpdateSchema(static::getSchema());
	}

	public static function insert()
	{
		return new InsertSchema(static::getSchema());
	}

	public static function initializeSchema()
	{
		if ( ! self::$schema)
		{
			self::$schema = new Schema(get_called_class());
		}
	}
}
