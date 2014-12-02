<?php

namespace Harp\Harp\Query;

use Harp\Harp\Config;
// use Harp\Harp\Model\Models;
use Harp\Query\DB;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Insert extends \Harp\Query\Insert
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(DB $db, Config $config)
    {
        parent::__construct($db);

        $this->config = $config;
        $this->into($config->getTable());

    }
    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    // /**
    //  * @param  Models $models
    //  * @return Insert         $this
    //  */
    // public function models(Models $models)
    // {
    //     $columns = $this->getConfig()->getFields();
    //     $columnKeys = array_flip($columns);

    //     $this->columns($columns);

    //     foreach ($models as $model) {
    //         $data = $model->getProperties();
    //         $model->getConfig()->getSerializers()->serialize($data);
    //         $values = array_intersect_key($data, $columnKeys);
    //         $this->values(array_values($values));
    //     }

    //     return $this;
    // }

    // public function executeModels(Models $models)
    // {
    //     $this
    //         ->models($models)
    //         ->execute();

    //     $lastInsertId = $this->getLastInsertId();

    //     foreach ($models as $model) {
    //         $model->setId($lastInsertId);
    //         $lastInsertId += 1;
    //     }
    // }
}
