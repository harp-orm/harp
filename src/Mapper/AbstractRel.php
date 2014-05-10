<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel
{
    protected $name;
    protected $foreignStore;
    protected $store;

    abstract public function update(AbstractNode $parent, AbstractLink $link);
    abstract public function hasForeign(array $nodes);
    abstract public function loadForeign(array $nodes);
    abstract public function linkToForeign(array $nodes, array $foreign);
    abstract public function loadFromData(array $data);

    public function __construct($name, StoreInterface $store, StoreInterface $foreignStore, array $options = array())
    {
        $this->name = $name;
        $this->foreignStore = $foreignStore;
        $this->Store = $store;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStore()
    {
        return $this->Store;
    }

    public function getForeignStore()
    {
        return $this->foreignStore;
    }

    public function loadForeignForNodes(array $nodes)
    {
        if ($this->hasForeign($nodes)) {
            return $this->loadForeign($nodes);
        } else {
            return array();
        }
    }
}
