<?php

namespace Harp\Harp\Query;

use Harp\Query;
use Harp\Harp\AbstractRepo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Insert extends \Harp\Query\Insert {

    use JoinRelTrait;

    /**
     * @var AbstractRepo
     */
    private $repo;

    public function __construct(AbstractRepo $repo)
    {
        $this->repo = $repo;
        $this->into($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }
    /**
     * @return AbstractRepo
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
            $data = $model->getProperties();
            $model->getRepo()->getSerializers()->serialize($data);
            $values = array_intersect_key($data, $columnKeys);
            $this->values(array_values($values));
        }

        return $this;
    }
}
