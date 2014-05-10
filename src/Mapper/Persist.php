<?php namespace CL\Luna\Mapper;

use CL\Luna\Util\Objects;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Persist
{
    public static function groupByStore(SplObjectStorage $nodes)
    {
        return Objects::groupBy($nodes, function($node) {
            return $node->getStore();
        });
    }

    public static function nodes(LinkedNodes $nodes)
    {
        $nodes
            ->expandWithLinked()
            ->deleteRels()
            ->expandWithLinked();

        self::persist($nodes->getDeleted(), [NodeEvent::DELETE], function ($Store, $nodes) {
            $Store->delete($nodes);
        });

        self::persist($nodes->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function ($Store, $nodes) {
            $Store->insert($nodes);
        });

        $nodes->updateRels();

        self::persist($nodes->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function ($Store, $nodes) {
            $Store->update($nodes);
        });
    }

    public static function persist(SplObjectStorage $nodes, array $events, Closure $yield)
    {
        $groups = self::groupByStore($nodes);

        foreach ($groups as $Store) {
            foreach ($events as $event) {
                $Store->dispatchBeforeEvent($nodes, $event);
            }

            $yield($Store, $groups->getInfo());

            foreach ($events as $event) {
                $Store->dispatchAfterEvent($nodes, $event);
            }
        }
    }
}
