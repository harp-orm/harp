<?php

namespace Harp\Harp\Query;

use Harp\Harp\Repo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Delete extends \Harp\Query\Delete {

    use JoinRelTrait;

    /**
     * @var Repo
     */
    private $repo;

    public function __construct(Repo $repo)
    {
        $this->repo = $repo;

        $this->from($repo->getTable());

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
        $key = $this->getRepo()->getPrimaryKey();
        $ids = $models->pluckProperty($key);
        $this->whereIn($key, $ids);

        return $this;
    }

}
