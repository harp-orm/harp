<?php namespace CL\Luna\ModelQuery;

use CL\Atlas\SQL\SQL;
use CL\Atlas\Query\UpdateQuery;
use CL\Luna\Schema\Schema;
use CL\Luna\Model\ModelEvent;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends UpdateQuery implements SetInterface {

    use ModelQueryTrait;

    public function __construct(Schema $schema)
    {
        $this
            ->setSchema($schema)
            ->table($schema->getTable());
    }

    protected $models;

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

        if ($values) {
            $this
                ->set($values)
                ->where([$primaryKey => $ids]);
        }

        return $this;
    }

    public function setModels(array $models)
    {
        $this->models = $models;
        $models = Arr::index($models, $this->schema->getPrimaryKey());
        $changes = Arr::invoke($models, 'getChanges');
        $this->setMultiple($changes);

        return $this;
    }

    public function execute()
    {
        $this->addToLog();

        $result = parent::execute();

        foreach ($this->models as $model)
        {
            $model
                ->resetOriginals()
                ->dispatchEvent(ModelEvent::UPDATE)
                ->dispatchEvent(ModelEvent::PERSIST);
        }

        return $result;
    }
}
