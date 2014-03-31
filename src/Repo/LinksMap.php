<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinksMap
{
    private $links;

    public function __construct()
    {
        $this->links = new SplObjectStorage();
    }

    public function get(Model $model)
    {
        if ($this->links->contains($model)) {
            return $this->links[$model];
        } else {
            $links = new Links();
            $this->links[$model] = $links;
            return $links;
        }
    }

    public function isEmpty(Model $model)
    {
        return (! $this->has($model) or $this->get($model)->isEmpty());
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
            Repo::getInstance()->loadLinkArray($model::getRel($name), [$model]);

        }

        return $links->get($name);
    }

    public function setLink(Model $model, $name, AbstractLink $link)
    {
        $this->get($model)->add($name, $link);

        return $this;
    }

    public function update(Model $model)
    {
        if ( ! $this->isEmpty($model))
        {
            $this->get($model)->updateAll($model);
        }
    }

    public function getLinkedModels(Model $model)
    {
        $models = new SplObjectStorage();

        $models->attach($model);

        if ( ! $this->isEmpty($model))
        {
            $linkedModels = $this->get($model)->getAllModels();

            $models->addAll($linkedModels);
        }

        return $models;
    }

    public function dump()
    {
        echo "\n";
        foreach ($this->links as $model) {
            echo get_class($model).' ('.$model->getId().")\n";

            $links = $this->links->getInfo();

            if ( ! $links->isEmpty()) {
                foreach ($links->all() as $name => $link) {
                    echo '  - '.$name.': '.get_class($link)."\n";
                }
            }
        }
    }
}
