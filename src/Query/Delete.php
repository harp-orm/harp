<?php

namespace CL\Luna\Query;

use CL\Luna\AbstractDbRepo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends \CL\Atlas\Query\Delete {

    use JoinRelTrait;

    protected $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;

        $this->from($repo->getTable());

        parnet::__construct($repo->getDbInstance());
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $ids = $models->pluckProperty($this->getRepo()->getPrimaryKey());
        $this->whereKeys($ids);

        return $this;
    }

}
