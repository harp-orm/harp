<?php

namespace CL\Luna;

use CL\Luna\Field\AbstractField;
use CL\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Fields {

    protected $items = array();

    /**
     * @param  AbstractField $item
     * @return Rels        $this
     */
    public function add(AbstractField $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    /**
     * @return AbstractField[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param  AbstractField[] $items
     * @return Rels          $this
     */
    public function set(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * @param  string           $name
     * @return AbstractField|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function callSaveData(array $data)
    {
        foreach ($data as $name => & $value) {
            $field = $this->get($name);

            if ($field !== null) {
                $value = $field->save($value);
            }
        }

        return $data;
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function callLoadData(array $data)
    {
        foreach ($data as $name => & $value) {
            $field = $this->get($name);

            if ($field !== null) {
                $value = $field->load($value);
            }
        }

        return $data;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->items);
    }
}
