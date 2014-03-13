<?php namespace CL\Luna\Util;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class Collection
{
    protected $items;

    public function __construct(array $items = NULL)
    {
        if ($items)
        {
            $this->set($items);
        }
    }

    public function all()
    {
        return $this->items;
    }

    public function set(array $items)
    {
        array_map([$this, 'add'], $items);

        return $this;
    }

    public function has($name)
    {
        return isset($this->items[$name]);
    }

    public function get($name)
    {
        if ($this->has($name))
        {
            return $this->items[$name];
        }
    }

    public function isEmpty()
    {
        return empty($this->items);
    }
}
