<?php

namespace Harp\Harp\Query;

use Harp\Harp\Config;
use Harp\Query\DBl;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Update extends \Harp\Query\Update
{
    use JoinRelTrait;

    /**
     * @var Config
     */
    private $config;

    public function __construct(DB $db, Config $config)
    {
        parent::__construct($db);

        $this->config = $config;
        $this->table($config->getTable());
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    // public function models(Models $models)
    // {
    //     $changes = array();

    //     foreach ($models as $model) {
    //         $data = $model->getChanges();
    //         $model->getConfig()->getSerializers()->serialize($data);
    //         $changes[$model->getId()] = $data;
    //     }

    //     $key = $this->config->getPrimaryKey();

    //     $this
    //         ->setMultiple($changes, $key)
    //         ->whereIn($key, array_keys($changes));

    //     return $this;
    // }

    // public function model(AbstractModel $model)
    // {
    //     $data = $model->getChanges();
    //     $model->getConfig()->getSerializers()->serialize($data);

    //     $this
    //         ->set($data)
    //         ->where($model->getConfig()->getPrimaryKey(), $model->getId());

    //     return $this;
    // }

    // public function executeModels(Models $models)
    // {
    //     if ($models->count() == 1) {
    //         $this->model($models->getFirst());
    //     } else {
    //         $this->models($models);
    //     }

    //     $this->execute();
    // }
}
