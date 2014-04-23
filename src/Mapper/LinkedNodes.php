<?php namespace CL\Luna\Mapper;

use CL\Luna\Util\Storage;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkedNodes extends SplObjectStorage
{
    protected $linkMap;

    public function __construct(LinkMap $linkMap)
    {
        $this->linkMap = $linkMap;
    }

    public function getDeleted()
    {
        return Storage::filter($this, function($node) {
            return $node->isDeleted();
        });
    }

    public function getPending()
    {
        return Storage::filter($this, function($node) {
            return $node->isPending();
        });
    }

    public function getChanged()
    {
        return Storage::filter($this, function($node) {
            return ($node->isChanged() AND $node->isPersisted());
        });
    }

    public function add(AbstractNode $node)
    {
        $this->attach($node);

        return $this;
    }

    public function updateRels()
    {
        $this->linkMap->updateNodes($this);

        return $this;
    }

    public function expandWithLinked()
    {
        foreach ($this as $node) {
            $this->linkMap->addAllRecursive($this, $node);
        }

        return $this;
    }
}
