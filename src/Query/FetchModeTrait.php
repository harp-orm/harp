<?php

namespace CL\Luna\Query;

use PDO;
use PDOStatement;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait FetchModeTrait {

    public function setFetchMode(PDOStatement $statement)
    {
        if ($this->getRepo()->getPolymorphic()) {
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->getRepo()->getModelClass(), $this->getModelConstructArguments());
        }
    }

    public function getModelConstructArguments()
    {
        return null;
    }
}
