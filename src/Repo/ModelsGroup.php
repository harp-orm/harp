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

    public static function persist(SplObjectStorage $models, $query_type)
    {
        $groups = Storage::groupBy($models, function($model) {
            return $model->getSchema();
        });

        foreach ($groups as $schema) {

            $models = Storage::toArray($groups->getInfo());

            $schema
                ->getQuery($query_type)
                ->setModels($models)
                ->execute();
        }
    }
}
