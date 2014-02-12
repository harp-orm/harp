<?php namespace CL\Luna\DB;

use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractEagerLoaded;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;
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

	public function joinRel($rel, $alias, $type)
	{
		$this->getSchema()->getRels()[$rel]->joinRel($alias, $type);
	}

	public function executeAndLoad($rels)
	{
		$result = $this->execute()->fetchAll();

		$rels = Arr::toAssoc( (array) $rels);

		AbstractEagerLoaded::eagerLoad($this->getSchema(), $result, $rels);

		return $result;
	}

	public function execute()
	{
		if (Log::getEnabled())
		{
			Log::add($this->humanize());
		}

		$pdoStatement = parent::execute();

		$pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $this->getSchema()->getModelClass(), [NULL, TRUE]);

		return $pdoStatement;
	}
}
