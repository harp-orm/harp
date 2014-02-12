<?php namespace CL\Luna\DB;

use CL\Luna\Schema\Schema;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SchemaTrait {

	protected $schema;

	public function setSchema(Schema $schema)
	{
		$this->schema = $schema;
		$this->db = DB::instance($schema->getDb());

		return $this;
	}

	public function getSchema()
	{
		return $this->schema;
	}

	public function whereKey($key)
	{
		return $this->where([$this->getSchema()->getPrimaryKey() => $key]);
	}
}
