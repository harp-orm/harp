<?php

namespace Harp\Db\Query;

use Harp\Query;
use Harp\Db\AbstractDbRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use CL\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends \Harp\Query\Insert {

    use JoinRelTrait;

    protected $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->into($repo->getTable());

        parent::__construct($repo->getDbInstance());
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
