<?php namespace CL\Luna\Mapper;

use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface SchemaInterface
{
    public function update(SplObjectStorage $nodes);
    public function delete(SplObjectStorage $nodes);
    public function insert(SplObjectStorage $nodes);
    public function dispatchBeforeEvent(SplObjectStorage $nodes, $event);
    public function dispatchAfterEvent(SplObjectStorage $nodes, $event);
    public function newInstance($properties = null, $status = AbstractNode::PENDING);
}
