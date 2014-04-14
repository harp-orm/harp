<?php namespace CL\Luna\Mapper;

use CL\Luna\Util\Storage;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PersistQueue
{
    protected $nodes;
    protected $links;

    public function __construct(LinkMap $links)
    {
        $this->nodes = new SplObjectStorage();
        $this->links = $links;
    }

    public function add(SplObjectStorage $nodes)
    {
        foreach ($nodes as $node) {
            $this->addNode($node);
        }

        return $this;
    }

    public function all()
    {
        return $this->nodes;
    }

    public function clear()
    {
        $this->nodes = new SplObjectStorage();

        return $this;
    }

    public function addNode(AbstractNode $node)
    {
        $this->nodes->attach($node);

        return $this;
    }

    public function getDeleted()
    {
        return Storage::filter($this->nodes, function($node) {
            return $node->isDeleted();
        });
    }

    public function getPending()
    {
        return Storage::filter($this->nodes, function($node) {
            return $node->isPending();
        });
    }

    public function getChanged()
    {
        return Storage::filter($this->nodes, function($node) {
            return ($node->isChanged() AND $node->isPersisted());
        });
    }

    public static function groupBySchema(SplObjectStorage $nodes)
    {
        return Storage::groupBy($nodes, function($node) {
            return $node->getSchema();
        });
    }

    public function flush()
    {
        self::persist($this->getDeleted(), [NodeEvent::DELETE], function ($schema, $nodes) {
            $schema->delete($nodes);
        });

        $this->links->updateNodes($this->nodes);

        self::persist($this->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function ($schema, $nodes) {
            $schema->insert($nodes);
        });

        $this->links->updateNodes($this->nodes);

        self::persist($this->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function ($schema, $nodes) {
            $schema->update($nodes);
        });

        return $this;
    }

    public static function persist(SplObjectStorage $nodes, array $events, Closure $yield)
    {
        $groups = self::groupBySchema($nodes);

        foreach ($groups as $schema) {
            foreach ($events as $event) {
                $schema->dispatchBeforeEvent($nodes, $event);
            }

            $yield($schema, $groups->getInfo());

            foreach ($events as $event) {
                $schema->dispatchAfterEvent($nodes, $event);
            }
        }
    }
}
