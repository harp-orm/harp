<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
    private $map;

    public function getAll(array $models)
    {
        return array_map([$this, 'get'], $models);
    }

    public static function modelUnqiueKey(Model $model)
    {
        return self::getUniqueKey($model->getSchema(), $model->getId());
    }

    public static function getUniqueKey(Schema $shema, $id)
    {
        return $shema->getTable().'|'.$id;
    }

    public function get(Model $model)
    {
        if ($model->isPersisted()) {
            $key = self::modelUnqiueKey($model);

            if ($this->hasKey($key))
            {
                return $this->getKey($key);
            }
            else
            {
                $this->setKey($key, $model);
                return $model;
            }
        }

        return $model;
    }

    public function hasKey($key)
    {
        return isset($this->map[$key]);
    }

    public function getKey($key)
    {
        return $this->map[$key];
    }

    public function setKey($key, Model $model)
    {
        return isset($this->map[$key]);
    }

    public function set(Model $model)
    {
        $this->setKey(self::modelUnqiueKey($model), $Model);
    }

    public function has(Model $model)
    {
        return $this->hasKey(self::modelUnqiueKey($model));
    }
}
