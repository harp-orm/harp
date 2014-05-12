<?php

namespace CL\Luna\Mapper;

use InvalidArgumentException;
use ReflectionClass;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
    private $nodes;
    private $nodeClass;

    public function __construct(ReflectionClass $nodeClass)
    {
        $this->nodeClass = $nodeClass;
    }

    public function get(AbstractNode $node)
    {
        if ( ! $this->nodeClass->isInstance($node)) {
            throw new InvalidArgumentException(
                sprintf('Node Must be of %s', $this->nodeClass->getName())
            );
        }

        if ($node->isPersisted()) {
            $key = $node->getId();

            if (isset($this->nodes[$key])) {
                $node = $this->nodes[$key];
            } else {
                $this->nodes[$key] = $node;
            }
        }

        return $node;
    }

    public function getArray(array $nodes)
    {
        return array_map(function($node){
            return $this->get($node);
        }, $nodes);
    }

    public function clear()
    {
        $this->nodes = null;
    }
}
