<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\SQL\SQL;
use CL\Atlas\Query;
use CL\Luna\Model\Store;
use CL\Luna\Util\Objects;
use CL\Luna\Util\Arr;
use CL\Luna\ModelQuery;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends Query\Update implements SetInterface {

    use ModelQueryTrait;
    use SoftDeleteTrait;

    public function __construct(Store $Store)
    {
        $this
            ->setStore($Store)
            ->table($Store->getTable());

        $this->setSoftDelete($this->getStore()->getSoftDelete());
    }

    public function setModels(SplObjectStorage $models)
    {
        if ($models->count() > 1) {
            $models = Objects::index($models, $this->getStore()->getPrimaryKey());
            $changes = Arr::invoke($models, 'getChanges');

            $this
                ->setMultiple($changes, $this->getStore()->getPrimaryKey())
                ->where($this->Store->getPrimaryKey(), array_keys($changes));
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

        $this->addToLog();

        return parent::execute();
    }
}
