<?php

namespace CL\Luna\Rel;

use CL\Atlas\SQL\SQL;
use CL\Luna\Model\Schema;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class RelJoinCondition extends SQL
{
    public function __construct($table, $foreign_table, array $conditions, Schema $schema = null)
    {
        $parts = [];

        foreach ($conditions as $foreignColumn => $column)
        {
            $parts []= "{$foreign_table}.{$foreignColumn} = {$table}.{$column}";
        }

        if ($schema->getSoftDelete())
        {
            $parts []= $foreign_table.'.'.Schema::SOFT_DELETE_KEY.' IS NULL';
        }

        $this->content = 'ON '.implode(' AND ', $parts);
    }
}
