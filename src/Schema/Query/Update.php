<?php namespace CL\Luna\Schema\Query;

use CL\Atlas\SQL\SQL;
use CL\Atlas\Query\UpdateQuery;
use CL\Luna\Schema\Schema;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends UpdateQuery implements SetModelsInterface{

    use QueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->table($schema->getTable());
    }

    public function setMultiple(array $values)
    {
        $primaryKey = $this->getSchema()->getPrimaryKey();
        $values = array_filter($values);
        $ids = array_keys($values);
        $changedRows = count($values);

        if ($changedRows > 1)
        {
            $values = Arr::flipNested($values);

            foreach ($values as $column => & $changes)
            {
                if (($uniqueChanges = array_unique($changes)) AND (count($uniqueChanges) === 1 AND count($changes) === $changedRows))
                {
                    $changes = reset($uniqueChanges);
                }
                else
                {
                    $cases = join(' ', array_fill(0, count($changes), 'WHEN ? THEN ?'));

                    $value = "CASE {$primaryKey} {$cases} ELSE {$column} END";
                    $parameters = Arr::disassociate($changes);

                    $changes = new SQL($value, $parameters);
                }
            }
        }
        else
        {
            $values = reset($values);
        }

        return $this
            ->set($values)
            ->where([$primaryKey => $ids]);
    }

    public function setModels(array $models)
    {
        $models = Arr::index($models, $this->schema->getPrimaryKey());
        $changes = Arr::invoke($models, 'getChanges');
        $this->setMultiple($changes);

        return $this;
    }

    public function execute()
    {
        if (Log::getEnabled())
        {
            Log::add($this->humanize());
        }

        return parent::execute();
    }
}
