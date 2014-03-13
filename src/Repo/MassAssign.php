<?php namespace CL\Luna\Repo;

use CL\Luna\Model\Model;
use CL\Luna\Model\LinkOne;
use CL\Luna\Model\LinkMany;
use CL\Luna\Model\LinkInterface;
use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;
use CL\Luna\Util\Arr;
use CL\Luna\Rel\AbstractRel;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class MassAssign
{
    public static function getPropertiesData(Schema $schema, array $data)
    {
        return array_diff_key($data, $schema->getRels()->all());
    }

    public static function getRelsData(Schema $schema, array $data)
    {
        return array_intersect_key($data, $schema->getRels()->all());
    }

    public static function getModelFromData(Schema $schema, array $data)
    {
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

    public static function getModel(Schema $schema, $data, $permitted)
    {
        $model = self::getModelFromData($schema, $data);

        (new MassAssign($data, $permitted))
            ->on($model);

        return $model;
    }

    protected $permitted;
    protected $data;
    const UNSAFE = 'unsafe';

    function __construct(array $data, $permitted)
    {
        if (is_array($permitted))
        {
            $this->permitted = Arr::toAssoc($permitted);

            $this->data = array_intersect_key($data, $this->permitted);
        }
        elseif ($permitted === self::UNSAFE)
        {
            $this->data = $data;
            $this->permitted = $permitted;
        }
        else
        {
            throw new InvalidArgumentException('Permitted can be an array or "MassAssign::UNSAFE"');
        }
    }

    public static function onLink(AbstractRel $rel, LinkInterface $link, $data, $permitted)
    {
        if ($link instanceof LinkOne)
        {
            $link->set(self::getModel($rel->getForeignSchema(), $data, $permitted));
        }
        elseif ($link instanceof LinkMany)
        {
            $link->removeAll($link);

            foreach ($data as $dataItem)
            {
                $link->attach(self::getModel($rel->getForeignSchema(), $dataItem, $permitted));
            }
        }
    }

    public function getPermittedFor($relName)
    {
        return isset($this->permitted[$relName]) ? $this->permitted[$relName] : self::UNSAFE;
    }

    public function on(Model $model)
    {
        $schema = $model->getSchema();

        $model->setProperties(self::getPropertiesData($schema, $this->data));

        $relsData = self::getRelsData($schema, $this->data);

        foreach ($relsData as $relName => $data)
        {
            $rel = $schema->getRel($relName);
            $link = $model->getLink($rel);

            self::onLink($rel, $link, $data, $this->getPermittedFor($relName));
        }
    }
}
