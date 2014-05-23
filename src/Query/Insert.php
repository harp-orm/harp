<?php

namespace CL\Luna\Query;

use CL\Atlas\Query;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends \CL\Atlas\Query\Insert {

    use JoinRelTrait;

    protected $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->into($repo->getTable());

        parnet::__construct($repo->getDbInstance());
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $columns = $this->getRepo()->getFields();
        $columnKeys = array_flip($columns);

        $this->columns($columns);

        foreach ($models as $model) {
            $values = array_intersect_key($model->getProperties(), $columnKeys);
            $this->values(array_values($values));
        }

        return $this;
    }
}
