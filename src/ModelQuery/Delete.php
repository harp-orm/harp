<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\Query;
use CL\Luna\Model\Store;
use CL\atlas\SQL\SQL;
use CL\Luna\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends Query\Delete implements SetInterface {

    use ModelQueryTrait;
    use SoftDeleteTrait;

    public function __construct(Store $store)
    {
        $this
            ->setStore($store)
            ->from($store->getTable());

        $this->setSoftDelete($store->getSoftDelete());
    }

    public function execute()
    {
        if ($this->getSoftDelete()) {
            return $this->convertToSoftDelete()->execute();
        } else {
            return parent::execute();
        }
    }

    public function convertToSoftDelete()
    {
        $store = $this->getStore();
        $query = (new Update($store));

        if ($this->getOrder()) {
            $query->setOrder($this->getOrder());
        }

        if ($this->getLimit()) {
            $query->setLimit($this->getLimit());
        }

        if ($this->getJoin()) {
            $query->setJoin($this->getJoin());
        }

        if ($this->getWhere()) {
            $query->setWhere($this->getWhere());
        }

        $query
            ->setTable($this->getTable() ?: $this->getFrom())
            ->set([Store::SOFT_DELETE_KEY => new SQL('CURRENT_TIMESTAMP')])
            ->where($store->getTable().'.'.Store::SOFT_DELETE_KEY, null);

        return $query;
    }

    public function setModels(SplObjectStorage $models)
    {
        $ids = Objects::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}
