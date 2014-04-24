<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class City extends Model implements LocationInterface {

    use SchemaTrait;

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return $this->loadRelLink('country')->get();
    }

    public function setCountry(Country $country)
    {
        return $this->loadRelLink('country')->set($country);
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ])
            ->setRels([
                new Rel\BelongsTo('country', $schema, Country::getSchema()),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
