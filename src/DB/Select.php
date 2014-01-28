<?php namespace CL\Luna\DB;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends \CL\Atlas\Query\SelectQuery {

	use ScopedTrait;

	protected $fetchClass;

	public function setFetchClass($class)
	{
		$this->fetchClass = (string) $class;

		return $this;
	}

	public function getFetchCLass()
	{
		return $this->fetchClass;
	}

	public function execute()
	{
		$pdoStatement = parent::execute();

		if ($this->getFetchCLass())
		{
			$pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $this->getFetchCLass(), array(NULL, TRUE));
		}

		return $pdoStatement;
	}
}
