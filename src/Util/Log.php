<?php namespace CL\Luna\Util;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Log
{
	protected static $callback;
	protected static $enabled = FALSE;
	protected static $items;

	public static function setEnabled($enabled)
	{
		static::$enabled = (bool) $enabled;
	}

	public static function getEnabled()
	{
		return static::$enabled;
	}

	public static function all()
	{
		return static::$items;
	}

	public static function setCallback($callback)
	{
		static::$callback = $callback;
	}

	public static function add($message)
	{
		if (static::$enabled)
		{
			if (static::$callback)
			{
				static::$callback($message);
			}
			else
			{
				static::$items []= $message;
			}
		}
	}
}
