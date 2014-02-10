<?php namespace CL\Luna\DB;

use CL\Luna\Model\Schema;
use CL\Luna\Rel\EagerLoader;
use CL\Atlas\Query\SelectQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SelectSchema extends SelectQuery {

	use ScopedTrait;
	use SchemaTrait;

	public function __construct(Schema $schema)
	{
		$this
			->setSchema($schema)
			->from($schema->getTable());
	}

	public function whereKey($key)
	{
		return $this->where([$this->getSchema()->getPrimaryKey() => $key]);
	}

	public function joinRel($rel, $alias, $type)
	{
		$this->getSchema()->getRels()[$rel]->joinRel($alias, $type);
	}

	public function executeAndLoad($rels)
	{
		$result = $this->execute()->fetchAll();
		$schema = $this->getSchema();

		if ($rels)
		{
			foreach ($rels as $relName)
			{
				$rel = $schema->getRels()[$relName];

				$loader = new EagerLoader($result, $rel);
				$loader->execute();
			}
		}

		return $result;
	}

	public function execute()
	{
		$pdoStatement = parent::execute();

		$pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), array(NULL, TRUE));

		return $pdoStatement;
	}
}
