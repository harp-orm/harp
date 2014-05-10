<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Store;
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
    use FetchModeTrait;

    public function __construct(Store $Store)
    {
        $this
            ->setStore($Store)
            ->from($Store->getTable())
            ->column($Store->getTable().'.*');

        $this->setSoftDelete($this->getStore()->getSoftDelete());
    }


    protected static function loadRels($Store, $models, $rels)
    {
        foreach ($rels as $relName => $childRels) {
            $rel = $Store->getRel($relName);

            $foreign = Repo::get()->loadRel($rel, $models);

            if ($childRels) {
                self::loadRels($rel->getForeignStore(), $foreign, $childRels);
            }
        }
    }

    public function loadWith($rels)
    {
        $models = $this->load();

        $rels = Arr::toAssoc((array) $rels);

        self::loadRels($this->getStore(), $models, $rels);

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
            ->fetchAll(PDO::FETCH_COLUMN, ''.$this->getStore()->getPrimaryKey());
    }

    public function loadCount()
    {
        $store = $this->getStore();

        return $this
            ->clearColumns()
            ->column("COUNT({$store->getTable()}.{$store->getPrimaryKey()})", 'countAll')
            ->execute()
                ->fetchColumn();
    }

    public function loadFirst()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->store->newInstance(null, AbstractNode::VOID);
    }

    public function loadRaw()
    {
        if ($this->getStore()->getPolymorphic()) {
            $this->prependColumn($this->getStore()->getTable().'.polymorphicClass');
        }

        $this->applySoftDelete();

        $pdoStatement = $this->execute();

        $this->setFetchMode($pdoStatement);

        $models = $pdoStatement->fetchAll();

        $this->getStore()->dispatchAfterEvent($models, NodeEvent::LOAD);

        return $models;
    }
}
