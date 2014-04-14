<?php namespace CL\Luna\Mapper;

use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface RelInterface
{
    public function update(AbstractNode $parent, AbstractLink $link);
    public function loadForeignNodes(array $nodes);
    public function loadForeignLinks(array $nodes, array $foreign, Closure $yield);
}
