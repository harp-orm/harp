<?php namespace CL\Luna\Repo;

use CL\Luna\Util\ObjectStorage;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelsGroup extends ObjectStorage
{
    public function add(Model $model)
    {
        $this->attach($model);

        if ( ! $model->isEmptyLinks())
        {
            $links = $model->getLinks();

            foreach ($links as $rel)
            {
                foreach ($links->getInfo()->getAll() as $model)
                {
                    $this->add($model);
                }
            }
        }

        return $this;
    }

    public function getDeleted()
    {
        return $this->filter(function($model) {
            return $model->isDeleted();
        });
    }

    public function getPending()
    {
        return $this->filter(function($model) {
            return $model->isPending();
        });
    }

    public function getChanged()
    {
        return $this->filter(function($model) {
            return ($model->isChanged() AND ! $model->isDeleted() AND ! $model->isNotLoaded());
        });
    }

    public function updateLinks()
    {
        foreach ($this as $model)
        {
            $model->updateLinks();
        }

        return $this;
    }

    public function persistDeleted()
    {
        $deleted = $this->getDeleted()->groupBySchema();

        foreach ($deleted as $schema)
        {
            $schema
                ->getDeleteQuery()
                    ->setModels($deletedSchemas->getInfo()->toArray())
                    ->execute();
        }

        return $this;
    }

    public function persistChanged()
    {
        $changed = $this->getChanged()->groupBySchema();

        foreach ($changed as $schema)
        {
            $schema
                ->getUpdateQuery()
                    ->setModels($changed->getInfo()->toArray())
                    ->execute();
        }

        return $this;
    }

    public function persistPending()
    {
        $new = $this->getPending()->groupBySchema();

        foreach ($new as $schema)
        {
            $schema
                ->getInsertQuery()
                    ->setModels($new->getInfo()->toArray())
                    ->execute();
        }
        return $this;
    }

    public function groupBySchema()
    {
        return $this->groupBy(function($item) {
            return $item->getSchema();
        });
    }
}
