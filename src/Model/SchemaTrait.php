<?php namespace CL\Luna\Model;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SchemaTrait
{
	protected static $schema;

	public static function getTable()
	{
		return self::$schema->getTable();
	}

	public static function getDb()
	{
		return self::$schema->getDb();
	}

	public static function getFields()
	{
		return self::$schema->getFields();
	}

	public static function getRels()
	{
		return self::$schema->getRels();
	}

	public static function getValidators()
	{
		return self::$schema->getVaidators();
	}

	public static function initializeSchema()
	{
		if ( ! self::$schema)
		{
			self::$schema = new Schema(get_called_class());
		}
	}
}
