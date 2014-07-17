<?php

namespace Harp\Harp;

use Harp\Harp\Repo\IdentityMap;
use Harp\Harp\Repo\Event;
use Harp\Harp\Repo\LinkMap;
use Harp\Harp\Repo\AbstractLink;
use Harp\Harp\Model\State;
use Harp\Harp\Model\RepoProxyTrait;
use Harp\Harp\Model\Models;
use Harp\Query\DB;
use Harp\Util\Arr;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Repo
{
    use ConfigProxyTrait;
    use RepoProxyTrait;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * @var LinkMap
     */
    private $linkMap;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param string $modelClass
     */
    function __construct($modelClass)
    {
        $this->config = new Config($modelClass);
        $this->identityMap = new IdentityMap($this);
        $this->linkMap = new LinkMap($this);
    }

    /**
     * @return IdentityMap
     */
    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    /**
     * @return LinkMap
     */
    public function getLinkMap()
    {
        return $this->linkMap;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return DB
     */
    public function getDbInstance()
    {
        return DB::get($this->getDb());
    }

    /**
     * @param  AbstractModel $model
     * @return AbstractRepo  $this
     */
    public function initializeModel(AbstractModel $model)
    {
        $this->getSerializers()->unserialize($model);

        if ($this->getInherited()) {
            $model->class = $this->getModelClass();
        }

        $this->dispatchAfterEvent($model, Event::CONSTRUCT);
    }

    /**
     * Add an already loaded link. Used in eager loading.
     *
     * @param  AbstractLink             $link
     * @return Repo         $this
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function addLink(AbstractLink $link)
    {
        $this->getLinkMap()->addLink($link);

        return $this;
    }

    /**
     * @param  AbstractModel            $model
     * @param  string                   $name
     * @return AbstractLink
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function loadLink(AbstractModel $model, $name, $flags = null)
    {
        $links = $this->getLinkMap()->get($model);

        if (! $links->has($name)) {
            $this->loadRelFor(new Models([$model]), $name, $flags);
        }

        return $links->get($name);
    }

    /**
     * Load models for a given relation.
     *
     * @param  Models                   $models
     * @param  string                   $relName
     * @return Models
     * @throws InvalidArgumentException If $relName does not belong to repo
     */
    public function loadRelFor(Models $models, $relName, $flags = null)
    {
        $rel = $this->getRelOrError($relName);

        $foreign = $rel->loadModelsIfAvailable($models, $flags);

        $rel->linkModels($models, $foreign, function (AbstractLink $link) {
            $class = get_class($link->getModel());
            $class::getRepo()->addLink($link);
        });

        return $foreign;
    }

    /**
     * Load all the models for the provided relations. This is the meat of the eager loading
     *
     * @param  Models           $models
     * @param  array            $rels
     * @param  int              $flags
     * @return Repo $this
     */
    public function loadAllRelsFor(Models $models, array $rels, $flags = null)
    {
        $rels = Arr::toAssoc($rels);

        foreach ($rels as $relName => $childRels) {
            $foreign = $this->loadRelFor($models, $relName, $flags);

            if ($childRels) {
                $rel = $this->getRel($relName);
                $rel->getRepo()->loadAllRelsFor($foreign, $childRels, $flags);
            }
        }

        return $this;
    }

    /**
     * Call all the events associated with model updates. Perform the update itself.
     *
     * @param  Models           $models
     * @return Repo $this
     */
    public function updateModels(Models $models)
    {
        foreach ($models as $model) {
            if ($model->isSoftDeleted()) {
                $this->dispatchBeforeEvent($model, Event::DELETE);
            } else {
                $this->dispatchBeforeEvent($model, Event::UPDATE);
                $this->dispatchBeforeEvent($model, Event::SAVE);
            }
        }

        $this->updateAll()->executeModels($models);

        foreach ($models as $model) {

            $model->resetOriginals();

            if ($model->isSoftDeleted()) {
                $this->dispatchAfterEvent($model, Event::DELETE);
            } else {
                $this->dispatchAfterEvent($model, Event::UPDATE);
                $this->dispatchAfterEvent($model, Event::SAVE);
            }
        }

        return $this;
    }

    /**
     * Call all the events associated with model deletion. Perform the deletion itself.
     *
     * @param  Models           $models
     * @return Repo $this
     */
    public function deleteModels(Models $models)
    {
        foreach ($models as $model) {
            $this->dispatchBeforeEvent($model, Event::DELETE);
        }

        $this->deleteAll()->executeModels($models);

        foreach ($models as $model) {
            $this->dispatchAfterEvent($model, Event::DELETE);
        }

        return $this;
    }

    /**
     * Call all the events associated with model insertion. Perform the insertion itself.
     *
     * @param  Models           $models
     * @return Repo $this
     */
    public function insertModels(Models $models)
    {
        foreach ($models as $model) {
            $this->dispatchBeforeEvent($model, Event::INSERT);
            $this->dispatchBeforeEvent($model, Event::SAVE);
        }

        $this->insertAll()->executeModels($models);

        foreach ($models as $model) {
            $model
                ->resetOriginals()
                ->setState(State::SAVED);

            $this->dispatchAfterEvent($model, Event::INSERT);
            $this->dispatchAfterEvent($model, Event::SAVE);
        }

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @param  int           $event
     * @return Repo  $this
     */
    public function dispatchBeforeEvent($model, $event)
    {
        $this->getEventListeners()->dispatchBeforeEvent($model, $event);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @param  int           $event
     * @return Repo  $this
     */
    public function dispatchAfterEvent($model, $event)
    {
        $this->getEventListeners()->dispatchAfterEvent($model, $event);

        return $this;
    }

    /**
     * @param  array         $fields
     * @param  int           $state
     * @return AbstractModel
     */
    public function newModel($fields = null, $state = State::PENDING)
    {
        return $this->getReflectionModel()->newInstance($fields, $state);
    }

    /**
     * @param  array         $fields
     * @return AbstractModel
     */
    public function newVoidModel($fields = null)
    {
        return $this->getReflectionModel()->newInstance($fields, State::VOID);
    }

    /**
     * @return Repo
     */
    public function getRootRepo()
    {
        return $this->getRootConfig()->getRepo();
    }
}
