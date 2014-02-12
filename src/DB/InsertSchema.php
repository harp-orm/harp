<?php namespace CL\Luna\DB;

use CL\Atlas\Query\InsertQuery;
use CL\Luna\Schema\Schema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends InsertQuery {

	use ScopedTrait;
	use SchemaTrait;

	public function __construct(Schema $schema)
	{
		$this
			->setSchema($schema)
			->into($schema->getTable());
	}
}
