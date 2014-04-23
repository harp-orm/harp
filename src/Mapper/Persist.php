<?php namespace CL\Luna\Mapper;

use CL\Luna\Util\Storage;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Persist
{
    public static function groupBySchema(SplObjectStorage $nodes)
    {
        return Storage::groupBy($nodes, function($node) {
            return $node->getSchema();
        });
    }

    public static function nodes(LinkedNodes $nodes)
    {
        $nodes
            ->expandWithLinked()
            ->updateRels()
            ->expandWithLinked();

        self::persist($nodes->getDeleted(), [NodeEvent::DELETE], function ($schema, $nodes) {
            $schema->delete($nodes);
        });

        self::persist($nodes->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function ($schema, $nodes) {
            $schema->insert($nodes);
        });

        $nodes->updateRels();

        self::persist($nodes->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function ($schema, $nodes) {
            $schema->update($nodes);
        });
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
