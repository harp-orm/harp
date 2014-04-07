<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Model\ModelEvent;
use CL\Luna\ModelQuery\Select;
use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Repo
{
    private static $links;
    private static $map;

    public static function getLinks()
    {
        if (self::$links === null) {
            self::$links = new LinksMap();
        }

        return self::$links;
    }

    public static function getMap()
    {
        if (self::$map === null) {
            self::$map = new IdentityMap();
        }

        return self::$map;
    }

    public static function persistArray(array $models)
    {
        $models = new SplObjectStorage();

        foreach ($models as $model) {
            $models->addAll(self::getLinks()->getLinkedModels($model));
        }

        self::persistModels($models);
    }

    public static function persist(Model $model)
    {
        $models = self::getLinks()->getLinkedModels($model);

        self::persistModels($models);
    }

    public static function persistModels(SplObjectStorage $models)
    {
        $deleted = ModelsGroup::filterDeleted($models);
        ModelsGroup::dispatchEvents($deleted, [ModelEvent::BEFORE_DELETE]);
        ModelsGroup::persist($deleted, Schema::DELETE);
        ModelsGroup::dispatchEvents($deleted, [ModelEvent::AFTER_DELETE]);

        self::getLinks()->updateAll($models);

        $pending = ModelsGroup::filterPending($models);
        ModelsGroup::dispatchEvents($pending, [ModelEvent::BEFORE_INSERT, ModelEvent::BEFORE_PERSIST]);
        ModelsGroup::persist($pending, Schema::INSERT);
        ModelsGroup::dispatchEvents($pending, [ModelEvent::AFTER_INSERT, ModelEvent::AFTER_PERSIST]);

        self::getLinks()->updateAll($models);

        $changed = ModelsGroup::filterChanged($models);
        ModelsGroup::dispatchEvents($changed, [ModelEvent::BEFORE_UPDATE, ModelEvent::BEFORE_PERSIST]);
        ModelsGroup::persist($changed, Schema::UPDATE);
        ModelsGroup::dispatchEvents($changed, [ModelEvent::AFTER_UPDATE, ModelEvent::AFTER_PERSIST]);
    }

    public static function getModel(Model $model)
    {
        return self::getMap()->get($model);
    }

    public static function getLink(Model $model, $name)
    {
        return self::getLinks()->getLink($model, $name);
    }

    public static function loadModels(Select $select)
    {
        $models = $select->execute()->fetchAll();

        $loadedModels = self::getMap()->getAll($models);

        return $loadedModels;
    }

    public static function loadModel(Schema $schema, $id)
    {
        $key = IdentityMap::getUniqueKey($schema, $id);

        if (self::getMap()->hasKey($key))
        {
            return self::getMap()->getKey($key);
        }
        else
        {
            return self::getMap()->get(
                $schema->getSelectQuery()->whereKey($id)->first()
            );
        }
    }

    public static function loadRels(Schema $schema, array $models, array $rels)
    {
        foreach ($rels as $relName => $childRelNames)
        {
            $rel = $schema->getRel($relName);

            $relatedModels = self::loadLinks($rel, $models);

            if ($childRelNames)
            {
                self::loadRels($rel->getForeignSchema(), $relatedModels, $childRelNames);
            }
        }
    }

    public static function loadLinks(AbstractRel $rel, array $models)
    {
        $foreign = $rel->loadForeignModels($models);

        $foreign = self::getMap()->getAll($foreign);

        $rel->groupForeignModels($models, $foreign, function($model, $link) use ($rel) {
            self::getLinks()->setLink($model, $rel->getName(), $link);
        });

        return $foreign;
    }
}
