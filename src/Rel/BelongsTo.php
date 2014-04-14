<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Mapper;
use CL\Luna\Schema\Schema;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractOne
{
    protected $key;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getForeignKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function linkForeignKey(Mapper\AbstractNode $foreign)
    {
        return $foreign->{$this->getForeignKey()};
    }

    public function linkKey(Mapper\AbstractNode $model)
    {
        return $model->{$this->getKey()};
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->get()->isPersisted())
        {
            $model->{$this->getKey()} = $link->get()->getId();
        }
    }

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }
}
