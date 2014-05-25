<?php

namespace CL\Luna\Query;

use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends \CL\Atlas\Query\Select {

    use JoinRelTrait;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this
            ->from($repo->getTable())
            ->column($repo->getTable().'.*');

        parent::__construct($repo->getDbInstance());
    }

    public function getRepo()
    {
        return $this->repo;
    }
}
