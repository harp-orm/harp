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
class PersistQueue extends SplObjectStorage
{
    public function getDeleted()
    {
        return Objects::filter($this, function ($node) {
            return $node->isDeleted();
        });
    }

    public function getPending()
    {
        return Objects::filter($this, function ($node) {
            return $node->isPending();
        });
    }

    public function getChanged()
    {
        return Objects::filter($this, function ($node) {
            return ($node->isChanged() AND $node->isPersisted());
        });
    }

    public function add(AbstractNode $node)
    {
        $this->attach($node);

        return $this;
    }

    public function addWithLinked(AbstractNode $node)
    {
        if (! $this->contains($node)) {
            $this->add($node);

            $nodeLinks = $node->getRepo()->getLinkMap()->get($node);
            foreach ($nodeLinks->getNodes() as $linkedNode) {
                $this->add($linkedNode);
            }
        }

        return $this;
    }

    public function set(SplObjectStorage $nodes)
    {
        foreach ($nodes as $node) {
            $this->add($node);
        }

        return $this;
    }

    public function eachLink(Closure $yield)
    {
        foreach ($this as $node) {
            $linkMap = $node->getRepo()->getLinkMap();

            if ($linkMap->has($node)) {
                $links = $linkMap->get($node)->all();

                if ($links) {
                    foreach ($links as $link) {
                        $yield($node, $link);
                    }
                }
            }
        }

        return $this;
    }

    public function addDeletedLinks()
    {
        return $this
            ->eachLink(function (AbstractNode $node, AbstractLink $link) {
                if ($link->getRel() instanceof RelDeleteInterface) {
                    $this->set($link->getRel()->delete($node, $link));
                }
            });
    }

    public function addInsertedLinks()
    {
        return $this
            ->eachLink(function (AbstractNode $node, AbstractLink $link) {
                if ($link->getRel() instanceof RelInsertInterface) {
                    $this->set($link->getRel()->insert($node, $link));
                }
            });
    }

    public function updateLinks()
    {
        return $this
            ->eachLink(function (AbstractNode $node, AbstractLink $link) {
                if ($link->getRel() instanceof RelUpdateInterface) {
                    $link->getRel()->update($node, $link);
                }
            });
    }


    public static function groupByRepo(SplObjectStorage $nodes)
    {
        return Objects::groupBy($nodes, function($node) {
            return $node->getRepo();
        });
    }

    public function execute()
    {
        $this->addDeletedLinks();

        self::persist($this->getDeleted(), [NodeEvent::DELETE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
            $repo->delete($nodes);
        });

        $this->addInsertedLinks();

        self::persist($this->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
            $repo->insert($nodes);
        });

        $this->updateLinks();

        self::persist($this->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
            $repo->update($nodes);
        });
    }

    public static function persist(SplObjectStorage $nodes, array $events, Closure $yield)
    {
        $groups = self::groupByRepo($nodes);

        foreach ($groups as $repo) {
            foreach ($events as $event) {
                $repo->dispatchBeforeEvent($nodes, $event);
            }

            $yield($repo, $groups->getInfo());

            foreach ($events as $event) {
                $repo->dispatchAfterEvent($nodes, $event);
            }
        }
    }
}
