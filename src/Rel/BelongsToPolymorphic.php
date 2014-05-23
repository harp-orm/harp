<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\Atlas\Query\AbstractQuery;
use BadMethodCallException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsToPolymorphic extends AbstractRelOne implements DbRelInterface
{
    protected $key;
    protected $repoKey;

    public function __construct($name, AbstractDbRepo $store, AbstractDbRepo $defaultForeignRepo, array $options = array())
    {
        $this->key = $name.'Id';
        $this->repoKey = $name.'Repo';

        parent::__construct($name, $store, $defaultForeignRepo, $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getRepoKey()
    {
        return $this->repoKey;
    }

    public function getForeignKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return true;
    }

    public function loadForeign(Models $models)
    {
        $groups = Arr::groupBy($models->toArray(), function($model){
            return $model->{$this->repoKey};
        });

        foreach ($groups as $repoClass => & $models) {

            $keys = Arr::pluckUniqueProperty($models, $this->key);

            if ($keys) {
                $models = $repoClass::get()->findAll()
                    ->where($this->getForeignKey(), $keys)
                    ->loadRaw();
            }
        }

        return Arr::flatten($groups);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return (
            $model->{$this->key} == $foreign->{$this->getForeignKey()}
            and $model->{$this->repoKey} == get_class($foreign->getRepo())
        );
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        $model->{$this->key} = $link->get()->getId();
        $model->{$this->repoKey} = get_class($link->get()->getRepo());
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        throw new BadMethodCallException('BelongsToPolymorphic does not support join');
    }
}
