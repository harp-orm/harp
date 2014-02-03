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

	public function getFetchClass()
	{
		return $this->fetchClass;
	}

	public function whereKey($key)
	{
		$primaryKey = call_user_func([$this->getFetchClass(), 'getPrimaryKey']);

		return $this->where([$primaryKey => $key]);
	}

	public function execute()
	{
		$pdoStatement = parent::execute();

		if ($this->getFetchClass())
		{
			$pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $this->getFetchClass(), array(NULL, TRUE));
		}

		return $pdoStatement;
	}
}
