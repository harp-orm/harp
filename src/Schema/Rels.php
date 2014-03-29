<?php namespace CL\Luna\Schema;

use CL\Luna\Rel\AbstractRel;
use CL\Luna\Util\Collection;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels extends Collection {

    public function add(AbstractRel $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    public function initialize(Schema $schema)
    {
        if ($this->items)
        {
            foreach ($this->items as $item)
            {
                $item
                    ->setSchema($schema)
                    ->initialize();
            }
        }
    }
}
