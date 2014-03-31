<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\Rels;
use CL\Luna\Repo\Repo;
use CL\Luna\Util\Arr;
use CL\Luna\Rel\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class MassAssign
{
    public function loadFromData(AbstractRel $rel, array $data)
    {
        $schema = $rel->getForeignSchema();

        if (isset($data[$schema->getPrimaryKey()]))
        {
            $id = $data[$schema->getPrimaryKey()];
            return Repo::getInstance()->loadModel($schema, $id);
        }
        else
        {
            return $schema->getModelReflection()->newInstance();
        }
    }

    public function loadModel(AbstractRel $rel, $data, $permitted)
    {
        $model = $this->loadFromData($rel, $data);

        new MassAssign($model, $permitted, $data);

        return $model;
    }

    public function __construct(Model $model, array $permitted, array $data)
    {
        $permitted = Arr::toAssoc($permitted);
        $rels = $model->getSchema()->getRels();

        $data = array_intersect_key($data, $permitted);
        $model->setFieldValues($this->extractPropertiesData($data, $rels));

        $data = $this->extractRelsData($data, $rels);

        foreach ($data as $relName => $relData) {
            $link = Repo::getInstance()->getLink($model, $relName);

            $this->setLink($model::getRel($relName), $link, $relData, $this->extractPermitted($permitted, $relName));
        }
    }

    public function setLink(AbstractRel $rel, AbstractLink $link, $data, $permitted)
    {
        if ($link instanceof LinkOne)
        {
            $link->set($this->loadModel($rel, $data, $permitted));
        }
        elseif ($link instanceof LinkMany)
        {
            $link->clear();

            foreach ($data as $dataItem)
            {
                $link->add($this->loadModel($rel, $dataItem, $permitted));
            }
        }
    }

    public function extractPropertiesData(array $data, Rels $rels)
    {
        return array_diff_key($data, $rels->all());
    }

    public function extractRelsData(array $data, Rels $rels)
    {
        return array_intersect_key($data, $rels->all());
    }

    public function extractPermitted($permitted, $name)
    {
        return isset($permitted[$name]) ? $permitted[$name] : array();
    }
}
