<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Schema;
use CL\Luna\Mapper\Repo;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\NodeEvent;
use CL\Luna\Util\Arr;
use CL\Atlas\Query;
use PDO;
use PDOStatement;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends Query\Select {

    use ModelQueryTrait;
    use SoftDeleteTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->from($schema->getTable())
            ->column($schema->getTable().'.*');

        $this->setSoftDelete($this->getSchema()->getSoftDelete());
    }


    protected static function loadRels($schema, $models, $rels)
    {
        foreach ($rels as $relName => $childRels) {
            $rel = $schema->getRel($relName);

            $foreign = Repo::get()->loadRel($rel, $models);

            if ($childRels) {
                self::loadRels($rel->getForeignSchema(), $foreign, $childRels);
            }
        }
    }

    public function loadWith($rels)
    {
        $models = $this->load();

        $rels = Arr::toAssoc((array) $rels);

        self::loadRels($this->getSchema(), $models, $rels);

        return $models;
    }

    public function load()
    {
        $models = $this->loadRaw();

        return Repo::get()->getCanonicalArray($models);
    }

    public function loadIds()
    {
        return $this
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN, ''.$this->getSchema()->getPrimaryKey());
    }

    public function loadCount()
    {
        $schema = $this->getSchema();

        return $this
            ->clearColumns()
            ->column("COUNT({$schema->getTable()}.{$schema->getPrimaryKey()})", 'countAll')
            ->execute()
                ->fetchColumn();
    }

    public function loadFirst()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->schema->newInstance(null, AbstractNode::NOT_LOADED);
    }

    public function execute()
    {
        $this->addToLog();

        return parent::execute();
    }

    public function setFetchMode(PDOStatement $statement)
    {
        if ($this->getSchema()->getPolymorphic()) {
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), $this->getModelConstructArguments());
        }
    }

    public function getModelConstructArguments()
    {
        return null;
    }

    public function loadRaw()
    {
        if ($this->getSchema()->getPolymorphic()) {
            $this->prependColumn($this->getSchema()->getTable().'.polymorphicClass');
        }

        $this->applySoftDelete();

        $pdoStatement = $this->execute();

        $this->setFetchMode($pdoStatement);

        $models = $pdoStatement->fetchAll();

        $this->getSchema()->dispatchAfterEvent($models, NodeEvent::LOAD);

        return $models;
    }
}
