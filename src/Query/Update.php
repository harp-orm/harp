<?php

namespace Harp\Harp\Query;

use Harp\Harp\AbstractRepo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends \Harp\Query\Update {

    use JoinRelTrait;

    /**
     * @var AbstractRepo
     */
    private $repo;

    public function __construct(AbstractRepo $repo)
    {
        $this->repo = $repo;
        $this->table($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return AbstractRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $changes = array();

        foreach ($models as $model) {
            $data = $model->getChanges();
            $model->getRepo()->getSerializers()->serialize($data);
            $changes[$model->getId()] = $data;
        }

        $key = $this->repo->getPrimaryKey();

        $this
            ->setMultiple($changes, $key)
            ->whereIn($key, array_keys($changes));

        return $this;
    }
}
