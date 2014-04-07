<?php namespace CL\Luna\Repo;

use CL\Luna\Util\Storage;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelsGroup
{
    public static function filterDeleted(SplObjectStorage $models)
    {
        return Storage::filter($models, function($model) {
            return $model->isDeleted();
        });
    }

    public static function filterPending(SplObjectStorage $models)
    {
        return Storage::filter($models, function($model) {
            return $model->isPending();
        });
    }

    public static function filterChanged(SplObjectStorage $models)
    {
        return Storage::filter($models, function($model) {
            return ($model->isChanged() AND $model->isPersisted());
        });
    }

    public static function groupBySchema(SplObjectStorage $models)
    {
        return Storage::groupBy($models, function($model) {
            return $model->getSchema();
        });
    }

    public static function getSchemas(SplObjectStorage $models)
    {
        $schemas = new SplObjectStorage();

        foreach ($models as $model) {
            $schemas->attach($model->getSchema());
        }

        return $schemas;
    }

    public static function hasCascadeRels(SplObjectStorage $models)
    {
        $schemas = self::getSchemas($models);

        foreach ($schemas as $schema) {
            if ($schema->getCascadeRels()) {
                return true;
            }
        }

        return false;
    }

    public static function dispatchEvents(SplObjectStorage $models, array $events)
    {
        foreach ($events as $event) {
            foreach ($models as $model) {
                $model->dispatchEvent($event);
            }
        }

        return $models;
    }

    public static function persist(SplObjectStorage $models, $query_type)
    {
        $groups = self::groupBySchema($models);

        foreach ($groups as $schema) {

            $models = Storage::toArray($groups->getInfo());

            $schema
                ->getQuery($query_type)
                ->setModels($models)
                ->execute();
        }

        return $models;
    }
}
