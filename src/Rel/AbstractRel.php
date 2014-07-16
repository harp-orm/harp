<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\Model\Models;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;
use Closure;

/**
 * The base class for all the relations. Actual relations should extend AbstractRelMany or AbstractRelOne.
 * The main idea is to load all the models associated with a given set of models.
 * That way eager loading works out of the box.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractRel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Repo
     */
    private $repo;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $inverseOf;

    abstract public function areLinked(AbstractModel $model, AbstractModel $foreignModel);
    abstract public function hasModels(Models $models);
    abstract public function loadModels(Models $models, $flags = null);
    abstract public function newLinkFrom(AbstractModel $model, array $links);
    abstract public function join(AbstractWhere $query, $parent);

    /**
     * Foreign repo is used to allow you to correctly return "void" models.
     * Even if your relation is polymorphic and can link to different repos, you should
     * provide a default repo.
     *
     * @param string $name        Unique rel name
     * @param Config $config
     * @param Repo   $repo
     * @param array  $properties  Added as is to the rel's properties.
     */
    public function __construct($name, Config $config, Repo $repo, array $properties = array())
    {
        $this->name = $name;
        $this->config = $config;
        $this->repo = $repo;

        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return string
     */
    public function getInverseOf()
    {
        return $this->inverseOf;
    }

    /**
     * @return AbstractRel|null
     */
    public function getInverseOfRel()
    {
        return $this->inverseOf
            ? $this->getRepo()->getRelOrError($this->inverseOf)
            : null;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param  Models $models
     * @return Models
     */
    public function loadModelsIfAvailable(Models $models, $flags = null)
    {
        if ($this->hasModels($models)) {
            $modelsArray = $this->loadModels($models, $flags);

            return new Models($modelsArray);
        } else {
            return new Models();
        }
    }

    /**
     * @param  string $column
     * @param  array  $keys
     * @param  int    $flags
     * @return \Harp\Harp\Find
     */
    public function findAllWhereIn($column, array $keys, $flags)
    {
        return $this->getRepo()
            ->findAll()
            ->whereIn($column, $keys)
            ->setFlags($flags);
    }

    /**
     * @return array
     */
    public function getSoftDeleteConditions()
    {
        $conditions = [];

        if ($this->getRepo()->getSoftDelete()) {
            $conditions["{$this->getName()}.deletedAt"] = new SQL('IS NULL');
        }

        return $conditions;
    }

    /**
     * Iterate models and foreign models one by one and and assign links based on the areLinked method
     * Yeild the resulted links one by one for further processing.
     *
     * @param Models  $models
     * @param Models  $foreign
     * @param Closure $yield   call for each link
     */
    public function linkModels(Models $models, Models $foreign, Closure $yield)
    {
        foreach ($models as $model) {

            $linked = [];

            foreach ($foreign as $foreignModel) {
                if ($this->areLinked($model, $foreignModel)) {
                    $linked []= $foreignModel;
                }
            }

            $link = $this->newLinkFrom($model, $linked);

            $yield($link);
        }
    }
}
