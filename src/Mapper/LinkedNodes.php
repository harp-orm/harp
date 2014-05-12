<?php

namespace CL\Luna\Mapper;

use CL\Luna\Util\Objects;
use SplObjectStorage;
use Closure;

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
        return Objects::filter($this, function($node) {
            return $node->isDeleted();
        });
    }

    public function getPending()
    {
        return Objects::filter($this, function($node) {
            return $node->isPending();
        });
    }

    public function getChanged()
    {
        return Objects::filter($this, function($node) {
            return ($node->isChanged() AND $node->isPersisted());
        });
    }

    public function add(AbstractNode $node)
    {
        $this->attach($node);

        return $this;
    }

    public function eachRel(Closure $yield)
    {
        $this->linkMap->eachRel($this, $yield);

        return $this;
    }

    public function deleteRels()
    {
        return $this
            ->eachRel(function($rel, AbstractNode $node, AbstractLink $link){
                if ($rel instanceof RelDeleteInterface) {
                    $rel->delete($node, $link);
                }
            })
            ->expandWithLinked();
    }

    public function insertRels()
    {
        return $this
            ->eachRel(function($rel, AbstractNode $node, AbstractLink $link){
                if ($rel instanceof RelInsertInterface) {
                    $rel->insert($node, $link);
                }
            })
            ->expandWithLinked();
    }

    public function updateRels()
    {
        return $this
            ->eachRel(function($rel, AbstractNode $node, AbstractLink $link){
                if ($rel instanceof RelUpdateInterface) {
                    $rel->update($node, $link);
                }
            })
            ->expandWithLinked();
    }
    public function expandWithLinked()
    {
        foreach ($this as $node) {
            $this->linkMap->addAllRecursive($this, $node);
        }

        return $this;
    }
}
