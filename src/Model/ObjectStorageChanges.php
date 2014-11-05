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
class StorageChanges
{
    /**
     * @var array
     */
    private $original;

    private $current;

    public function __construct(ObjectStorageChanges $storage)
    {
        $this->original = clone $storage;
        $this->current = $storage;
    }

    /**
     * @return SplObjectStorage
     */
    public function getOriginal()
    {
        return $this->original;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function isChanged()
    {
        return $this->original != $this->current;
    }

    /**
     * @return Models
     */
    public function getAdded()
    {
        $added = clone $this->current;
        $added->removeAll($this->original);

        return $added;
    }

    /**
     * @return Models
     */
    public function getRemoved()
    {
        $removed = clone $this->original;
        $removed->removeAll($this->current);

        return $removed;
    }

    /**
     * @return Models
     */
    public function getCurrentAndOriginal()
    {
        $all = clone $this->original;
        $all->addAll($this->current);

        return $all;
    }
}
