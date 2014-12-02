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
class Delete extends \Harp\Query\Delete
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

        $this->from($config->getTable());

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
    //     $key = $this->getConfig()->getPrimaryKey();
    //     $ids = $models->pluckProperty($key);
    //     $this->whereIn($key, $ids);

    //     return $this;
    // }

    // public function executeModels(Models $models)
    // {
    //     $this
    //         ->models($models)
    //         ->execute();
    // }
}
