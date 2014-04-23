<?php namespace CL\Luna\ModelQuery;

use CL\Atlas\Query;
use CL\Luna\Schema\Schema;
use CL\Luna\Model\ModelEvent;
use CL\Luna\Util\Storage;
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
        $ids = Storage::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}
