<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\SQL\SQL;
use CL\Atlas\Query;
use CL\Luna\Model\Schema;
use CL\Luna\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SoftDelete extends Query\Update implements SetInterface {

    use ModelQueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->table($schema->getTable())
            ->set([Schema::SOFT_DELETE_KEY => new SQL('CURRENT_TIMESTAMP')])
            ->where([$schema->getTable().'.'.Schema::SOFT_DELETE_KEY => NULL]);
    }

    public function setModels(SplObjectStorage $models)
    {
        $ids = Objects::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

    public function execute()
    {
        $this->addToLog();

        return parent::execute();
    }
}
