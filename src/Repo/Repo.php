<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\ModelQuery\Select;
use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Repo\AbstractLink;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Repo
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === NULL)
        {
            self::$instance = new Repo();
        }
        return self::$instance;
    }

    private $map;
    private $links;

    public function __construct()
    {
        $this->map = new IdentityMap();
        $this->links = new LinksMap();
    }

    public function getModel(Model $model)
    {
        $this->map->get($model);
    }

    public function loadModels(Select $select)
    {
        $models = $select->execute()->fetchAll();
        return $this->map->getAll($models);
    }

    public function loadModel(Schema $schema, $id)
    {
        $key = $this->map->getUniqueKey($schema, $id);

        if ($this->map->hasKey($key))
        {
            return $this->map->getKey($key);
        }
        else
        {
            return $this->map->get(
                $schema->getSelectQuery()->whereKey($id)->first()
            );
        }
    }

    public function loadLinks(Schema $schema, array $models, array $rels)
    {
        foreach ($rels as $relName => $childRelNames)
        {
            $rel = $schema->getRel($relName);

            $relatedModels = $this->loadLinkArray($rel, $models);

            if ($childRelNames)
            {
                $this->loadLinks($rel->getForeignSchema(), $relatedModels, $childRelNames);
            }
        }

        return $this;
    }

    public function loadLinkArray(AbstractRel $rel, array $models)
    {
        $select = $rel->getSelectForModels($models);

        $related = $select ? $this->loadModels($select) : array();

        $rel->setLinks($models, $related);

        return $related;
    }

    public function setLink(Model $model, $name, AbstractLink $link)
    {
        $this->links->setLink($model, $name, $link);

        return $this;
    }

    public function getLink(Model $model, $name)
    {
        return $this->links->getLink($model, $name);
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function updateLinks(Model $model)
    {
        $this->links->update($model);

        return $this;
    }

    public function persistArray(array $models)
    {
        array_walk($models, [$this, 'persist']);

        return $this;
    }

    public function persist(Model $model)
    {
        $models = new ModelsGroup();

        $models->addAll($this->links->getLinkedModels($model));

        $models
            ->persistDeleted()
            ->updateLinks()
            ->persistPending()
            ->updateLinks()
            ->persistChanged();

        return $this;
    }
}
