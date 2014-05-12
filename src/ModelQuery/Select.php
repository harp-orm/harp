<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\AbstractRepo;
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

    public function __construct(AbstractRepo $repo)
    {
        $this
            ->setRepo($repo)
            ->from($repo->getTable())
            ->column($repo->getTable().'.*');

        $this->setSoftDelete($this->getRepo()->getSoftDelete());
    }


    protected static function loadRels($repo, $models, $rels)
    {
        foreach ($rels as $relName => $childRels) {
            $rel = $repo->getRel($relName);

            $foreign = $repo->loadRel($rel, $models);

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

        return $this->getRepo()->getIdentityMap()->getArray($models);
    }

    public function loadIds()
    {
        return $this
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN, ''.$this->getRepo()->getPrimaryKey());
    }

    public function loadCount()
    {
        $repo = $this->getRepo();

        return $this
            ->clearColumns()
            ->column("COUNT({$repo->getTable()}.{$repo->getPrimaryKey()})", 'countAll')
            ->execute()
                ->fetchColumn();
    }

    public function loadFirst()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->getRepo()->newVoidInstance();
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
