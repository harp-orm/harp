<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Util\Objects;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Rel\AbstractRelMany;
use CL\Luna\Query\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyExclusive extends AbstractRelMany implements RelJoinInterface, DeleteInterface
{
    protected $foreignKey;

    public function __construct($name, AbstractDbRepo $repo, AbstractDbRepo $foreignRepo, array $options = array())
    {
        $this->foreignKey = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        $keys = Arr::pluckUniqueProperty($models, $this->getKey());
        return ! empty($keys);
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
            ->findAll()
            ->where(
                $this->foreignKey,
                Arr::pluckUniqueProperty($models, $this->getKey())
            )
            ->loadRaw();
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignRepo->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignRepo);

        $query->joinAliased($this->foreignRepo->getTable(), $this->getName(), $condition);
    }

    public function delete(AbstractModel $model, AbstractLink $link)
    {
        Objects::invoke($link->getRemoved(), 'delete');

        return $link->getRemoved();
    }

    public function update(AbstractModel $model, AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
