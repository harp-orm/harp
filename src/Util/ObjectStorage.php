<?php namespace CL\Luna\Util;

use SplObjectStorage;
use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ObjectStorage extends SplObjectStorage
{
    public function __construct(array $items = NULL)
    {
        if ($items)
        {
            $this->attachArray($items);
        }
    }

    public function toArray()
    {
        $items = [];

        foreach ($this as $item)
        {
            $items []= $item;
        }

        return $items;
    }

    public function map(Closure $function)
    {
        $mapped = [];

        foreach ($this as $index => $object)
        {
            $mapped[$index] = $function($object);
        }

        return $mapped;
    }

    public function attachArray(array $array)
    {
        foreach ($array as $item)
        {
            $this->attach($item);
        }

        return $this;
    }

    public function invoke($function_name)
    {
        $mapped = [];

        foreach ($this as $index => $object)
        {
            $mapped[$index] = $object->$function_name();
        }

        return $mapped;
    }

    public function groupBy(Closure $get_object)
    {
        $groups = new ObjectStorage();

        foreach ($this as $item)
        {
            $key = $get_object($item);

            if ($groups->contains($key))
            {
                $groups[$key]->attach($item);
            }
            else
            {
                $groups[$key] = new ObjectStorage([$item]);
            }
        }

        return $groups;
    }

    public function filter(Closure $filter)
    {
        $filtered = clone $this;

        foreach ($this as $object)
        {
            if ( ! $filter($object))
            {
                $filtered->detach($object);
            }
        }

        return $filtered;
    }
}
