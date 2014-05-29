<?php

namespace Harp\Db\Query;

use Harp\Query;
use Harp\Db\AbstractDbRepo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends \Harp\Query\Insert {

    use JoinRelTrait;

    /**
     * @var AbstractDbRepo
     */
    private $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->into($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }
    /**
     * @return AbstractDbRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param  Models $models
     * @return Insert         $this
     */
    public function models(Models $models)
    {
        $columns = $this->getRepo()->getFields();
        $columnKeys = array_flip($columns);

        $this->columns($columns);

        foreach ($models as $model) {
            $values = array_intersect_key($model->saveData()->getProperties(), $columnKeys);
            $this->values(array_values($values));
        }

        return $this;
    }
}
