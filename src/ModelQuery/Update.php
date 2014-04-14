<?php namespace CL\Luna\ModelQuery;

use CL\Atlas\SQL\SQL;
use CL\Atlas\Query;
use CL\Luna\Schema\Schema;
use CL\Luna\Model\ModelEvent;
use CL\Luna\Util\Storage;
use CL\Luna\Util\Arr;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends Query\Update implements SetInterface {

    use ModelQueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->table($schema->getTable());
    }

    public function setModels(SplObjectStorage $models)
    {
        if ($models->count() > 1) {
            $models = Storage::index($models, $this->getSchema()->getPrimaryKey());
            $changes = Arr::invoke($models, 'getChanges');

            $this
                ->setMultiple($changes, $this->getSchema()->getPrimaryKey())
                ->where([$this->schema->getPrimaryKey() => array_keys($changes)]);
        } else {
            $models->rewind();
            $model = $models->current();
            $this
                ->set($model->getChanges())
                ->where([$this->schema->getPrimaryKey() => $model->getId()]);
        }

        return $this;
    }

    public function execute()
    {
        $this->addToLog();

        return parent::execute();
    }
}
