<?php

namespace Harp\Db\Query;

use Harp\Db\AbstractDbRepo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends \Harp\Query\Delete {

    use JoinRelTrait;

    /**
     * @var AbstractDbRepo
     */
    private $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;

        $this->from($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return AbstractDbRepo
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
