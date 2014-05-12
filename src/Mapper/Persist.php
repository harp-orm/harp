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
class Persist
{
    public static function groupByRepo(SplObjectStorage $nodes)
    {
        return Objects::groupBy($nodes, function($node) {
            return $node->getRepo();
        });
    }

    public static function nodes(LinkedNodes $nodes)
    {
        $nodes->expandWithLinked();

        $nodes->deleteRels();

        self::persist($nodes->getDeleted(), [NodeEvent::DELETE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
            $repo->delete($nodes);
        });

        $nodes->insertRels();

        self::persist($nodes->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
            $repo->insert($nodes);
        });

        $nodes->updateRels();

        self::persist($nodes->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $nodes) {
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
