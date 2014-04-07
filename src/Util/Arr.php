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

    public static function toAssoc(array $array)
    {
        $converted = array();

        foreach ($array as $key => $value)
        {
            if (is_numeric($key))
            {
                $converted[$value] = NULL;
            }
            else
            {
                $converted[$key] = self::toAssoc( (array) $value);
            }
        }

      return $converted;
    }

    public static function invoke(array $arr, $method)
    {
        return array_map(function($item) use ($method) {
            return $item->$method();
        }, $arr);
    }

    public static function index(array $arr, $attribute)
    {
        $result = [];
        foreach ($arr as $item)
        {
            $result[$item->{$attribute}] = $item;
        }
        return $result;
    }

    public static function groupByInvoke(array $arr, $method)
    {
        $result = [];
        foreach ($arr as $item)
        {
            $result[$item->{$method}()] []= $item;
        }
        return $result;
    }

    public static function indexGroup(array $arr, $attribute)
    {
        $result = [];
        foreach ($arr as $item)
        {
            $result[$item->{$attribute}] []= $item;
        }
        return $result;
    }

    public static function extract(array $arr, $attribute)
    {
        return array_map(function($item) use ($attribute) {
            return $item->$attribute;
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

    public static function flatten(array $array)
    {
        $result = array();

        array_walk_recursive($array, function ($value, $key) use ( & $result) {
            if (is_numeric($key) OR is_object($value))
            {
                $result[] = $value;
            }
            else
            {
                $result[$key] = $value;
            }
        });

        return $result;
    }

    /**
     * Transpose 2 dimensional array:
     *
     * <pre>
     * array (                   |    |  array (
     *    1 => array(            |    |      'name' => array(
     *        'name' => 'val1',  |    |          1 => 'val1',
     *        'email' => 'val2', |    |          2 => 'val3',
     *    ),                     | to |      ),
     *    2 => array(            |    |      'email' => array(
     *        'name' => 'val3',  |    |          1 => 'val2',
     *        'email' => 'val4', |    |          2 => 'val4',
     *    ),                     |    |      )
     * )                         |    |  )
     * </pre>
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

    public static function groupBy($array, $callback, $preserve_keys = FALSE)
    {
        $grouped = array();

        foreach ($array as $i => $item)
        {
            $itemGroup = call_user_func($callback, $item, $i);

            if ( ! isset($grouped[$itemGroup]))
            {
                $grouped[$itemGroup] = array();
            }

            if ($preserve_keys)
            {
                $grouped[$itemGroup][$i] = $item;
            }
            else
            {
                $grouped[$itemGroup][] = $item;
            }
        }

        return $grouped;
    }
}
