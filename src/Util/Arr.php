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

	public static function invoke(array $arr, $method)
	{
		return array_map(function($item) use ($method) {
			return $item->$method();
		}, $arr);
	}

	public static function filterInvoke(array $arr, $method)
	{
		return array_filter($arr, function($item) use ($method) {
			return $item->$method();
		});
	}

	public static function disassociate(array $arr)
	{
		$result = [];

		foreach ($arr as $key => $value)
		{
			$result []= $key;
			$result []= $value;
		}

		return $result;
	}

	/**
	 * I'm sure this opration has some proper math name ....
	 */
	public static function flipNested(array $arr)
	{
		$result = [];

		foreach ($arr as $key => $values)
		{
			foreach ($values as $innerKey => $value)
			{
				$result[$innerKey][$key] = $value;
			}
		}

		return $result;
	}
}
