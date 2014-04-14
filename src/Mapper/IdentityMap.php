<?php namespace CL\Luna\Mapper;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
    private $nodes;

    public static function modelUnqiueKey(AbstractNode $node)
    {
        return self::getUniqueKey(get_class($node), $node->getId());
    }

    public static function getUniqueKey($class, $id)
    {
        return $class.'|'.$id;
    }

    public function get(AbstractNode $node)
    {
        if ($node->isPersisted()) {
            $key = self::modelUnqiueKey($node);

            if ($this->hasKey($key)) {
                $node = $this->getKey($key);
            } else {
                $this->setKey($key, $node);
            }
        }

        return $node;
    }

    public function hasKey($key)
    {
        return isset($this->nodes[$key]);
    }

    public function getKey($key)
    {
        return $this->nodes[$key];
    }

    public function setKey($key, AbstractNode $node)
    {
        return isset($this->nodes[$key]);
    }

    public function set(AbstractNode $model)
    {
        $this->setKey(self::modelUnqiueKey($model), $Model);
    }

    public function has(AbstractNode $model)
    {
        return $this->hasKey(self::modelUnqiueKey($model));
    }
}
