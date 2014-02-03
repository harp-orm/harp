<?php namespace CL\Luna\DB;

use CL\Atlas\SQL\SQL;
use CL\Luna\Util\Arr;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends \CL\Atlas\Query\UpdateQuery {

	use ScopedTrait;

	public function setMultiple($id_column, array $values)
	{
		$ids = array_keys($values);
		$values = Arr::flipNested($values);

		foreach ($values as $column => & $changes)
		{
			$cases = join(' ', array_fill(0, count($changes), 'WHEN ? THEN ?'));

			$value = "CASE {$id_column} {$cases} ELSE {$column} END";
			$parameters = Arr::disassociate($changes);

			$changes = new SQL($value, $parameters);
		}

		return $this
			->set($values)
			->where([$id_column => $ids]);
	}
}
