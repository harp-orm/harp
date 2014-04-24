<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\Query;
use CL\Luna\Model\Schema;
use CL\Luna\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends Query\Delete implements SetInterface {

    use ModelQueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->from($schema->getTable());
    }

    public function execute()
    {
        $this->addToLog();

        return parent::execute();
    }

    protected $models;

    public function setModels(SplObjectStorage $models)
    {
        $this->models = $models;
        $ids = Objects::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}
