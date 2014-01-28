<?php namespace CL\Luna\Util;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Arr
{
	public static function invokeObjects(array $arr, array $objects, $method)
	{
		foreach ($arr as $name => & $value)
		{
			if (isset($objects[$name]))
			{
				$value = $objects[$name]->$method($value);
			}
		}

		return $arr;
	}
}
