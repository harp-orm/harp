<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Mapper\AbstractDbRepo;
use CL\Luna\Util\Arr;
use CL\Atlas\Query;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Union extends Query\Union {

    use ModelQueryTrait;
    use FetchModeTrait;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->setRepo($repo);
    }


    public function load()
    {
        $models = $this->loadRaw();

        return $this->getRepo()->getIdentityMap()->getArray($models);
    }

    public function loadRaw()
    {
        if ($this->getRepo()->getPolymorphic()) {
            foreach ($this->getSelects() as $select) {
                $select->prependColumn($this->getRepo()->getTable().'.polymorphicClass');
            }
        }

        $pdoStatement = $this->execute();

        $this->setFetchMode($pdoStatement);

        return $pdoStatement->fetchAll();
    }
}
