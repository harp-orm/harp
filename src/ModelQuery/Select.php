<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Repo;
use CL\Luna\Mapper\MainRepo;
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

    public function __construct(Repo $Repo)
    {
        $this
            ->setRepo($Repo)
            ->from($Repo->getTable())
            ->column($Repo->getTable().'.*');

        $this->setSoftDelete($this->getRepo()->getSoftDelete());
    }


    protected static function loadRels($Repo, $models, $rels)
    {
        foreach ($rels as $relName => $childRels) {
            $rel = $Repo->getRel($relName);

            $foreign = MainRepo::get()->loadRel($rel, $models);

            if ($childRels) {
                self::loadRels($rel->getForeignRepo(), $foreign, $childRels);
            }
        }
    }

    public function loadWith($rels)
    {
        $models = $this->load();

        $rels = Arr::toAssoc((array) $rels);

        self::loadRels($this->getRepo(), $models, $rels);

        return $models;
    }

    public function load()
    {
        $models = $this->loadRaw();

        return MainRepo::get()->getCanonicalArray($models);
    }

    public function loadIds()
    {
        return $this
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN, ''.$this->getRepo()->getPrimaryKey());
    }

    public function loadCount()
    {
        $store = $this->getRepo();

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
        if ($this->getRepo()->getPolymorphic()) {
            $this->prependColumn($this->getRepo()->getTable().'.polymorphicClass');
        }

        $this->applySoftDelete();

        $pdoStatement = $this->execute();

        $this->setFetchMode($pdoStatement);

        $models = $pdoStatement->fetchAll();

        $this->getRepo()->dispatchAfterEvent($models, NodeEvent::LOAD);

        return $models;
    }
}
