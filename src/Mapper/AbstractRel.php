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
    protected $schema;

    abstract public function update(AbstractNode $parent, AbstractLink $link);
    abstract public function hasForeign(array $nodes);
    abstract public function loadForeign(array $nodes);
    abstract public function linkToForeign(array $nodes, array $foreign);

    public function __construct($name, SchemaInterface $schema, array $options = array())
    {
        $this->name = $name;
        $this->schema = $schema;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSchema()
    {
        return $this->schema;
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
