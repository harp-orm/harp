<?php namespace CL\Luna\ModelQuery;

use CL\Atlas\Query\DeleteQuery;
use CL\Luna\Schema\Schema;
use CL\Luna\Model\ModelEvent;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends DeleteQuery implements SetInterface {

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

        $result = parent::execute();

        if ($this->models)
        {
            foreach ($this->models as $model)
            {
                $model
                    ->dispatchEvent(ModelEvent::DELETE);
            }
        }

        return $result;
    }

    protected $models;

    public function setModels(array $models)
    {
        $this->models = $models;
        $ids = Arr::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}
