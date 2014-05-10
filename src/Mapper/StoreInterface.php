<?php

namespace CL\Luna\Mapper;

use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface StoreInterface
{
    public function getRel($name);
    public function update(SplObjectStorage $nodes);
    public function delete(SplObjectStorage $nodes);
    public function insert(SplObjectStorage $nodes);
    public function dispatchBeforeEvent($nodes, $event);
    public function dispatchAfterEvent($nodes, $event);
    public function newInstance($properties = null, $status = AbstractNode::PENDING);
}
