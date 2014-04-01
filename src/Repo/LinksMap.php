<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Util\Storage;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinksMap
{
    private $links;

    function __construct()
    {
        $this->links = new SplObjectStorage();
    }

    public function get(Model $model)
    {
        if ($this->links->contains($model)) {
            return $this->links[$model];
        } else {
            return $this->links[$model] = new Links();
        }
    }

    public function isEmpty(Model $model)
    {
        return (! $this->links->contains($model) or $this->links[$model]->isEmpty());
    }

    public function has(Model $model)
    {
        return $this->links->contains($model);
    }

    public function getLink(Model $model, $name)
    {
        $links = $this->get($model);

        if ( ! $links->has($name))
        {
            Repo::loadLinks($model->getSchema()->getRel($name), [$model]);
        }

        return $links->get($name);
    }

    public function setLink(Model $model, $name, AbstractLink $link)
    {
        $this->get($model)->add($name, $link);

        return $this;
    }

    public function updateAll(SplObjectStorage $models)
    {
        $models = Storage::filter($models, function($model){
            return ! $this->isEmpty($model);
        });

        foreach ($models as $model) {
            $this->get($model)->updateAll($model);
        }
    }

    public function getLinkedModels(Model $model)
    {
        $models = new SplObjectStorage();

        $models->attach($model);

        if ( ! $this->isEmpty($model))
        {
            $linkedModels = $this->get($model)->getModelsRecursive();

            $models->addAll($linkedModels);
        }

        return $models;
    }
}
