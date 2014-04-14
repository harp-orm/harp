<?php namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use CL\Luna\Schema\Schema;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractMany
{
    protected $foreignKey;
    protected $deleteOnRemove;

    public function __construct($name, Schema $schema, Schema $foreign_schema, array $options = array())
    {
        $this->foreignKey = $schema->getName().'Id';

        parent::__construct($name, $schema, $foreign_schema, $options);
    }

    public function getDeleteOnRemove()
    {
        return $this->deleteOnRemove;
    }

    public function setDeleteOnRemove($delete_on_remove)
    {
        $this->deleteOnRemove = (bool) $delete_on_remove;

        return $this;
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

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

    public function deleteModels(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $model->delete();
        }
    }

    public function setModels(SplObjectStorage $models, $key)
    {
        foreach ($models as $model) {
            $model->{$this->getForeignKey()} = $key;
        }
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $this->setModels($link->getAdded(), $model->{$this->getKey()});

        if ($this->getDeleteOnRemove()) {
            $this->deleteModels($link->getRemoved());
        } else {
            $this->setModels($link->getRemoved(), null);
        }
    }
}
