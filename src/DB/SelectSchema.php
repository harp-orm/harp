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
			self::eagerLoad($this->getSchema(), $rels, $result);
		}

		return $result;
	}

	public static function eagerLoad(Schema $schema, $rels, array $parents)
	{
		foreach ($rels as $key => $value)
		{
			$relName = is_numeric($key) ? $value : $key;

			$rel = $schema->getRel($relName);

			$loader = new EagerLoader($parents, $rel);
			$loader->execute();

			if ( ! is_numeric($key))
			{
				self::eagerLoad($rel->getForeignSchema(), $value, $loader->getChildren());
			}
		}
	}

	public function execute()
	{
		var_dump($this->humanize());
		$pdoStatement = parent::execute();

		$pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), array(NULL, TRUE));

		return $pdoStatement;
	}
}
