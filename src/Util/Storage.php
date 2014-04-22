<?php namespace CL\Luna\Util;

use SplObjectStorage;
use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Storage
{
    public static function combineArrays(array $arr, array $arr2, Closure $yield)
    {
        $items = new SplObjectStorage();

        foreach ($arr as $item) {
            foreach ($arr2 as $item2) {
                if ($yield($item, $item2)) {
                    $items->attach($item, $item2);
                }
            }
        }

        return $items;
    }

    public static function groupCombineArrays(array $arr, array $arr2, Closure $yield)
    {
        $groups = new SplObjectStorage();

        foreach ($arr as $item) {
            foreach ($arr2 as $item2) {
                if ($yield($item, $item2)) {
                    Storage::addNested($groups, $item, $item2);
                }
            }
        }

        return $groups;
    }

    public static function addNested(SplObjectStorage $storage, $parent, $child)
    {
        $current = $storage->contains($parent)
            ? $storage[$parent]
            : array();

        array_push($current, $child);

        $storage[$parent] = $current;

        return $storage;
    }

    public static function index(SplObjectStorage $storage, $property)
    {
        $result = [];
        foreach ($storage as $item)
        {
            $result[$item->{$property}] = $item;
        }
        return $result;
    }

    public static function invoke(SplObjectStorage $storage, $method)
    {
        $mapped = [];

        foreach ($storage as $object) {
            $mapped []= $object->$method();
        }

        return $mapped;
    }

    public static function filter(SplObjectStorage $storage, Closure $filter)
    {
        $filtered = new SplObjectStorage();

        foreach ($storage as $object) {
            if ($filter($object)) {
                $filtered->attach($object);
            }
        }

        return $filtered;
    }

    public static function setEach(SplObjectStorage $storage, array $properties)
    {
        foreach ($storage as $object) {
            foreach ($properties as $name => $value) {
                $object->$name = $value;
            }
        }

        return $storage;
    }


    public static function toArray(SplObjectStorage $storage)
    {
        $items = [];

        foreach ($storage as $item)
        {
            $items []= $item;
        }

        return $items;
    }

    public static function groupBy(SplObjectStorage $storage, Closure $get_group)
    {
        $groups = new SplObjectStorage();

        foreach ($storage as $item)
        {
            $key = $get_group($item);

            if ($groups->contains($key))
            {
                $groups[$key]->attach($item);
            }
            else
            {
                $group = new SplObjectStorage();
                $group->attach($item);
                $groups->attach($key, $group);
            }
        }

        return $groups;
    }
}
