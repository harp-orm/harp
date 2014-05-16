<?php

namespace CL\Luna\Query;

use CL\Atlas\Query;
use CL\Luna\AbstractDbRepo;
use CL\Util\Objects;
use CL\Util\Arr;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends Query\Update implements SetInterface {

    use ModelQueryTrait;
    use SoftDeleteTrait;

    public function __construct(AbstractDbRepo $store)
    {
        $this
            ->setRepo($store)
            ->table($store->getTable());

        $this->setSoftDelete($this->getRepo()->getSoftDelete());
    }

    public function setModels(SplObjectStorage $models)
    {
        if ($models->count() > 1) {
            $models = Objects::index($models, $this->getRepo()->getPrimaryKey());
            $changes = Arr::invoke($models, 'getChanges');

            $this
                ->setMultiple($changes, $this->getRepo()->getPrimaryKey())
                ->where($this->getRepo()->getPrimaryKey(), array_keys($changes));
        } else {
            $models->rewind();
            $model = $models->current();
            $this
                ->set($model->getChanges())
                ->whereKey($model->getId());
        }

        return $this;
    }

    public function execute()
    {
        $this->applySoftDelete();

        return parent::execute();
    }
}
