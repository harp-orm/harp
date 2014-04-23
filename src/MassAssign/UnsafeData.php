<?php namespace CL\Luna\MassAssign;

use CL\Luna\Model\Model;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Mapper\Repo;
use CL\Luna\Mapper\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class UnsafeData
{
    public static function assign(array $data, Model $model)
    {
        $data = new UnsafeData($data);
        $data->assignTo($model);

        return $model;
    }

    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function assignTo(Model $model)
    {
        $rels = $model->getSchema()->getRels()->all();

        $relsData = array_intersect_key($this->data, $rels);
        $propertiesData = array_diff_key($this->data, $rels);

        $model->setProperties($propertiesData);

        foreach ($relsData as $relName => $data) {
            $link = Repo::get()->loadLink($model, $relName);

            if ($link instanceof LinkOne) {
                $relModel = self::isLoaded($data) ? self::load($link->getRel(), $data) : $link->get();
                UnsafeData::assign($data, $relModel);
                $link->set($relModel);
            } else {
                $updatedItems = [];

                foreach ($data as $offset => $itemData) {
                    $relModel = self::isLoaded($itemData) ? self::load($link->getRel(), $itemData) : $link->getRel()->getForeignSchema()->newInstance();
                    UnsafeData::assign($itemData, $relModel);
                    $updatedItems []= $relModel;
                }

                $link->set($updatedItems);
            }
        }
    }

    public static function isLoaded(array $data)
    {
        return isset($data['_id']);
    }

    public static function load(AbstractRel $rel, array $data)
    {
        return $rel->getForeignSchema()->getSelectQuery()->whereKey($data['_id'])->first();
    }
}
