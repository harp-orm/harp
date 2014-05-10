<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\Query;
use CL\Luna\Model\Schema;
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

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->from($schema->getTable());

        $this->setSoftDelete($schema->getSoftDelete());
    }

    public function execute()
    {
        if ($this->getSoftDelete()) {
            $schema = $this->getSchema();

            $softDelete = (new Update($schema));

            if ($this->getOrder()) {
                $softDelete->setOrder($this->getOrder());
            }
            if ($this->getLimit()) {
                $softDelete->setLimit($this->getLimit());
            }
            if ($this->getJoin()) {
                $softDelete->setJoin($this->getJoin());
            }
            if ($this->getWhere()) {
                $softDelete->setWhere($this->getWhere());
            }

            $softDelete
                ->setTable($this->getTable() ?: $this->getFrom())
                ->set([Schema::SOFT_DELETE_KEY => new SQL('CURRENT_TIMESTAMP')])
                ->where($schema->getTable().'.'.Schema::SOFT_DELETE_KEY, null);

            return $softDelete->execute();
        } else {
            $this->addToLog();

            return parent::execute();
        }
    }

    protected $models;

    public function setModels(SplObjectStorage $models)
    {
        $ids = Objects::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}
