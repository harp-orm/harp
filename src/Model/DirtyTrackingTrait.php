<?php

namespace Harp\Harp\Model;

/**
 * This will add ability to check if public properties of an object have been "changed".
 * It is important to "setOriginals" early in the objects lifesycle (constructor).
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait DirtyTrackingTrait
{
    /**
     * @var array
     */
    private $originals = [];

    /**
     * @param array $originials
     */
    public function setOriginals(array $originials)
    {
        $this->originals = $originials;
    }

    /**
     * @return array
     */
    public function getOriginals()
    {
        return $this->originals;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getOriginal($name)
    {
        return isset($this->originals[$name]) ? $this->originals[$name] : null;
    }

    /**
     * Return change array(original, new)
     *
     * @param  string $name
     * @return array
     */
    public function getChange($name)
    {
        if ($this->hasChange($name)) {
            return [$this->getOriginal($name), $this->$name];
        }
    }

    /**
     * Return changes array(name1 => new value, name2 => new vlaue))
     *
     * @return array
     */
    public function getChanges()
    {
        $changes = [];

        foreach ($this->originals as $name => $original) {
            if ($this->hasChange($name)) {
                $changes[$name] = $this->$name;
            }
        }

        return $changes;
    }

    /**
     * Check if property differs from original
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasChange($name)
    {
        return ($this->$name != $this->getOriginal($name));
    }

    /**
     * Check if there are any changes
     *
     * @return boolean
     */
    public function isEmptyChanges()
    {
        foreach ($this->originals as $name => $orignial) {
            if ($this->hasChange($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Opposite of isEmptyChanges
     *
     * @return boolean
     */
    public function isChanged()
    {
        return ! $this->isEmptyChanges();
    }

    /**
     * Set originals to current properties of the model
     *
     * @return static
     */
    public function resetOriginals()
    {
        $this->originals = $this->getProperties();

        return $this;
    }

    /**
     * @param  DirtyTrackingTrait $object
     * @return array
     */
    public static function getPublicPropertiesOf($object)
    {
        return get_object_vars($object);
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return DirtyTrackingTrait::getPublicPropertiesOf($this);
    }

    /**
     * @param array $values
     */
    public function setProperties(array $values)
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }
}
