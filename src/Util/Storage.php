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
    public static function invoke(SplObjectStorage $storage, $function_name)
    {
        $mapped = [];

        foreach ($storage as $object) {
            $mapped []= $object->$function_name();
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
