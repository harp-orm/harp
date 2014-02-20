<?php namespace CL\Luna\EntityManager;

use CL\Luna\Schema\Schema;
use CL\Luna\Events\JobEvent;
use Closure;
/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractJob
{
	private $query;

	function __construct($query)
	{
		$this->query = $query;
	}

	public function execute()
	{
		return $this->query->execute();
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getSchema()
	{
		return $this->query->getSchema();
	}
}
