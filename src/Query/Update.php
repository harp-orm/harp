<?php

namespace CL\Luna\Query;

use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends \CL\Atlas\Query\Update {

    use JoinRelTrait;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->table($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $changes = array();

        foreach ($models as $model) {
            $changes[$model->getId()] = $model->getChanges();
        }

        $update
            ->setMultiple($changes, $this->repo->getPrimaryKey())
            ->whereKey(array_keys($changes));

        return $this;
    }
}
