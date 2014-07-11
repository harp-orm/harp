<?php

namespace Harp\Harp\Query;

use Harp\Harp\Repo;
use Harp\Harp\Model\Models;
use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Update extends \Harp\Query\Update {

    use JoinRelTrait;

    /**
     * @var Repo
     */
    private $repo;

    public function __construct(Repo $repo)
    {
        $this->repo = $repo;
        $this->table($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $changes = array();

        foreach ($models as $model) {
            $data = $model->getChanges();
            $model->getRepo()->getSerializers()->serialize($data);
            $changes[$model->getId()] = $data;
        }

        $key = $this->repo->getPrimaryKey();

        $this
            ->setMultiple($changes, $key)
            ->whereIn($key, array_keys($changes));

        return $this;
    }

    public function model(AbstractModel $model)
    {
        $data = $model->getChanges();
        $model->getRepo()->getSerializers()->serialize($data);

        $this
            ->set($data)
            ->where($model->getRepo()->getPrimaryKey(), $model->getId());

        return $this;
    }

    public function executeModels(Models $models)
    {
        if ($models->count() == 1) {
            $this->model($models->getFirst());
        } else {
            $this->models($models);
        }

        $this->execute();
    }
}
