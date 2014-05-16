<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\Luna\Query\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRelOne implements RelJoinInterface, UpdateInterface
{
    protected $key;

    public function __construct($name, AbstractDbRepo $store, AbstractDbRepo $foreignRepo, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    public function hasForeign(array $models)
    {
        $keys = Arr::pluckUniqueProperty($models, $this->key);

        return ! empty($keys);
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
            ->findAll()
            ->where(
                $this->getForeignKey(),
                Arr::pluckUniqueProperty($models, $this->key)
            )
            ->loadRaw();
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getForeignKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function update(AbstractModel $model, AbstractLink $link)
    {
        $model->{$this->getKey()} = $link->get()->getId();
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignRepo());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

}
