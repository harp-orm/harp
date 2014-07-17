<?php

namespace Harp\Harp\Repo;

use Harp\Util\Objects;
use Harp\Harp\Model\Models;
use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use InvalidArgumentException;
use Closure;

/**
 * Represnts Models for a specific repo.
 * Will throw exceptions if you try to add models from a different repo.
 * Also getNext() and getFirst() methods will return void models, instead of nulls
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoModels extends Models
{
    /**
     * Repo
     */
    private $repo;

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param Repo    $repo
     * @param AbstractModel[] $models
     */
    public function __construct(Repo $repo, array $models = null)
    {
        $this->repo = $repo;

        parent::__construct($models);
    }

    /**
     * @param  AbstractModel           $model
     * @return Models                  $this
     * @throws InvalidArgumentExtepion If $model not part of the repo
     */
    public function add(AbstractModel $model)
    {
        if (! $this->repo->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf('Model must be part of repo %s', $this->repo->getName())
            );
        }

        return parent::add($model);
    }

    /**
     * @param  Closure $filter
     * @return RepoModels
     */
    public function filter(Closure $filter)
    {
        $filtered = new RepoModels($this->repo);

        $filtered->addObjects(Objects::filter($this->all(), $filter));

        return $filtered;
    }

    /**
     * If model doesn't exist, return a void model
     *
     * @return AbstractModel
     */
    public function getFirst()
    {
        return parent::getFirst() ?: $this->getRepo()->newVoidModel();
    }

    /**
     * If model doesn't exist, return a void model
     *
     * @return AbstractModel
     */
    public function getNext()
    {
        return parent::getNext() ?: $this->getRepo()->newVoidModel();
    }
}
