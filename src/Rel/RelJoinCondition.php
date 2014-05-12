<?php

namespace CL\Luna\Rel;

use CL\Atlas\SQL\SQL;
use CL\Luna\Model\AbstractDbRepo;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class RelJoinCondition extends SQL
{
    public function __construct($table, $foreign_table, array $conditions, AbstractDbRepo $store = null)
    {
        $parts = [];

        foreach ($conditions as $foreignColumn => $column)
        {
            $parts []= "{$foreign_table}.{$foreignColumn} = {$table}.{$column}";
        }

        if ($store->getSoftDelete())
        {
            $parts []= $foreign_table.'.'.AbstractDbRepo::SOFT_DELETE_KEY.' IS NULL';
        }

        $this->content = 'ON '.implode(' AND ', $parts);
    }
}
