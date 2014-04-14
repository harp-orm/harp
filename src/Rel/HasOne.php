<?php namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractOne
{
    protected $foreignKey;

    public function __construct($name, Schema $schema, Schema $foreign_schema, array $options = array())
    {
        $this->foreignKey = $schema->getName().'Id';

        parent::__construct($name, $schema, $foreign_schema, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getPrimaryKey();
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
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = NULL;
        }
    }

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->join([$this->getForeignTable() => $this->getName()], $condition);
    }
}
