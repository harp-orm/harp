<?php

namespace Harp\Harp;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\AbstractLink;
use Closure;
use Countable;

/**
 * This model handles grouping of models for saving to the storage mechanism.
 * It will group models from the same repos, alongside linked models (traversed recursively).
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Save implements Countable
{
    /**
     * @var Models
     */
    private $models;

    /**
     * @param array $models
     */
    public function __construct(array $models = array())
    {
        $this->models = new Models();

        $this->addArray($models);
    }

    /**
     * Return all the models that are deleted (but not soft deleted)
     *
     * @return Models
     */
    public function getModelsToDelete()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return ($model->isDeleted() and ! $model->isSoftDeleted());
        });
    }

    /**
     * Return all the "pending" models
     *
     * @return Models
     */
    public function getModelsToInsert()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return $model->isPending();
        });
    }

    /**
     * Return all the models that are both saved and changed
     *
     * @return Models
     */
    public function getModelsToUpdate()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return ($model->isChanged() and ($model->isSaved() or $model->isSoftDeleted()));
        });
    }

    /**
     * Add only one model without traversing the linked models
     *
     * @param  AbstractModel $model
     * @return Save          $this
     */
    public function addShallow(AbstractModel $model)
    {
        $this->models->add($model);

        return $this;
    }

    /**
     * Add a model, traverse all the linked models recursively and add them too.
     *
     * @param  AbstractModel $model
     * @return Save
     */
    public function add(AbstractModel $model)
    {
        if (! $this->has($model)) {
            $this->addShallow($model);

            $modelLinks = $model->getRepo()->getLinkMap()->get($model);

            foreach ($modelLinks->getModels() as $linkedModel) {
                $this->add($linkedModel);
            }
        }

        return $this;
    }

    /**
     * @param  AbstractModel[] $models
     * @return Save            $this
     */
    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  Models $models
     * @return Save   $this
     */
    public function addAll(Models $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->models->has($model);
    }

    /**
     * Remove all the added models
     *
     * @return Save $this
     */
    public function clear()
    {
        $this->models->clear();

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->models->count();
    }

    /**
     * Iterate over all the links of the models
     * Each callback may return a Models object, in which case these models are added too.
     * This is useful for relations that modify additional models
     *
     * @param Closure $yield
     */
    public function eachLink(Closure $yield)
    {
        foreach ($this->models as $model) {
            $linkMap = $model->getRepo()->getLinkMap();

            if ($linkMap->has($model)) {
                $links = $linkMap->get($model)->all();

                foreach ($links as $link) {
                    if (($new = $yield($link))) {
                        $this->addAll($new);
                    }
                }
            }
        }
    }

    /**
     * Perform "delete" method on each link and gather the results
     */
    public function addFromDeleteRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->delete();
        });
    }

    /**
     * Perform "insert" method on each link and gather the results
     */
    public function addFromInsertRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->insert();
        });
    }

    /**
     * Perform "update" method on each link and gather the results
     */
    public function addFromUpdateRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->update();
        });
    }

    /**
     * Save all the models to the storage mechanism
     */
    public function execute()
    {
        $this->addFromDeleteRels();

        $this->getModelsToDelete()->byRepo(function (Repo $repo, Models $models) {
            $repo->deleteModels($models);
        });

        $this->addFromInsertRels();

        $this->getModelsToInsert()->assertValid()->byRepo(function (Repo $repo, Models $models) {
            $repo->insertModels($models);
        });

        $this->addFromUpdateRels();

        $this->getModelsToUpdate()->assertValid()->byRepo(function (Repo $repo, Models $models) {
            $repo->updateModels($models);
        });
    }
}
