<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Schema;
use CL\Luna\Mapper\Repo;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Util\Arr;
use CL\Atlas\Query;
use CL\Atlas\SQL\Aliased;
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

    public function eagerLoad($rels)
    {
        $models = $this->load();

        $rels = Arr::toAssoc((array) $rels);

        self::loadRels($this->getSchema(), $models, $rels);

        return $models;
    }

    public function prependColumn($column)
    {
        array_unshift($this->columns, new Aliased($column));

        return $this;
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

    public function load()
    {
        $models = $this->execute()->fetchAll();

        return Repo::get()->getCanonicalArray($models);
    }

    public function loadIds()
    {
        $this->addToLog();

        return parent::execute()
            ->fetchAll(PDO::FETCH_COLUMN, $this->getSchema()->getPrimaryKey());
    }

    public function first()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->schema->newInstance(null, AbstractNode::NOT_LOADED);
    }

    public function execute()
    {
        if ($this->getSchema()->getPolymorphic()) {
            $this->prependColumn($this->getSchema()->getTable().'.polymorphicClass');
        }

        $this->addToLog();

        $pdoStatement = parent::execute();

        if ($this->getSchema()->getPolymorphic()) {
            $pdoStatement->setFetchMode(
                PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE
            );
        } else {
            $pdoStatement->setFetchMode(
                PDO::FETCH_CLASS,
                $this->getSchema()->getModelClass()
            );
        }

        return $pdoStatement;
    }
}
