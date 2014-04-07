<?php namespace CL\Luna\ModelQuery;

use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use CL\Atlas\Query;
use PDO;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends Query\Select {

    use ModelQueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->from($schema->getTable())
            ->column($schema->getTable().'.*');
    }

    public function loadWith($rels)
    {
        $models = $this->load();

        $rels = Arr::toAssoc((array) $rels);

        Repo::loadRels($this->getSchema(), $models, $rels);

        return $models;
    }

    public function load()
    {
        return Repo::loadModels($this);
    }

    public function first()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->schema->newNotLoadedModel();
    }

    public function execute()
    {
        $this->addToLog();

        $pdoStatement = parent::execute();

        $pdoStatement->setFetchMode(PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), [NULL, Model::PERSISTED]);

        return $pdoStatement;
    }
}
